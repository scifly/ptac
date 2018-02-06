<?php

namespace App\Models;

use App\Events\MenuCreated;
use App\Events\MenuDeleted;
use App\Events\MenuMoved;
use App\Events\MenuUpdated;
use App\Http\Requests\MenuRequest;
use App\Models\MenuTab as MenuTab;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\Menu 菜单
 *
 * @property int $id
 * @property int|null $parent_id 父菜单ID
 * @property int $menu_type_id
 * @property string $name 菜单名称
 * @property string|null $uri
 * @property string|null $remark 菜单备注
 * @property int|null $media_id 图片ID
 * @property int|null $position 菜单所处位置
 * @property int|null $icon_id 图标ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Menu[] $children
 * @property-read Company $company
 * @property-read Corp $corp
 * @property-read Collection|Group[] $groups
 * @property-read Icon|null $icon
 * @property-read Media|null $media
 * @property-read MenuType $menuType
 * @property-read Menu|null $parent
 * @property-read School $school
 * @property-read Collection|\App\Models\Tab[] $tabs
 * @method static Builder|Menu whereCreatedAt($value)
 * @method static Builder|Menu whereEnabled($value)
 * @method static Builder|Menu whereIconId($value)
 * @method static Builder|Menu whereId($value)
 * @method static Builder|Menu whereMediaId($value)
 * @method static Builder|Menu whereMenuTypeId($value)
 * @method static Builder|Menu whereName($value)
 * @method static Builder|Menu whereParentId($value)
 * @method static Builder|Menu wherePosition($value)
 * @method static Builder|Menu whereRemark($value)
 * @method static Builder|Menu whereUpdatedAt($value)
 * @method static Builder|Menu whereUri($value)
 * @mixin Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\GroupMenu[] $groupMenus
 */
class Menu extends Model {

    // todo: needs to be optimized

    const SUPER_ROLES = ['运营', '企业', '学校'];
    # 不含子菜单的HTML模板
    const SIMPLE = '<li%s><a id="%s" href="%s" class="leaf"><i class="%s"></i> %s</a></li>';
    # 包含子菜单的HTML模板
    const TREE = <<<HTML
            <li class="treeview%s">
                <a href="#">
                    <i class="%s"></i> <span>%s</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
HTML;

    protected $fillable = [
        'parent_id', 'name', 'remark', 'uri',
        'menu_type_id', 'position', 'media_id',
        'action_id', 'icon_id', 'enabled',
    ];

    /**
     * 获取菜单所属类型
     *
     * @return BelongsTo
     */
    public function menuType() { return $this->belongsTo('App\Models\MenuType'); }

    /**
     * 返回菜单所属的媒体对象
     *
     * @return BelongsTo
     */
    public function media() { return $this->belongsTo('App\Models\Media'); }

    /**
     * 获取对应的公司对象
     *
     * @return HasOne
     */
    public function company() { return $this->hasOne('App\Models\Company'); }

    /**
     * 获取对应的企业对象
     *
     * @return HasOne
     */
    public function corp() { return $this->hasOne('App\Models\Corp'); }

    /**
     * 获取对应的学校对象
     *
     * @return HasOne
     */
    public function school() { return $this->hasOne('App\Models\School'); }

    /**
     * 获取指定菜单所属的所有角色对象
     *
     * @return BelongsToMany
     */
    public function groups() { return $this->belongsToMany('App\Models\Group', 'groups_menus'); }

    /**
     * 获取指定菜单包含的所有角色菜单对象
     *
     * @return HasMany
     */
    public function groupMenus() { return $this->hasMany('App\Models\GroupMenu'); }

    /**
     * 获取菜单包含的卡片
     *
     * @return BelongsToMany
     */
    public function tabs() {

        return $this->belongsToMany(
            'App\Models\Tab',
            'menus_tabs',
            'menu_id',
            'tab_id'
        );

    }

    /**
     * 获取上级菜单
     *
     * @return BelongsTo
     */
    public function parent() { return $this->belongsTo('App\Models\Menu', 'parent_id'); }

    /**
     * 获取图标
     *
     * @return BelongsTo
     */
    public function icon() { return $this->belongsTo('App\Models\Icon'); }

    /**
     * 获取指定菜单的子菜单
     *
     * @return HasMany
     */
    public function children() { return $this->hasMany('App\Models\Menu', 'parent_id', 'id'); }

    /**
     * 获取所有叶节点菜单
     *
     * @param null $rootMenuId
     * @return Collection|static[]
     */
    static function leaves($rootMenuId = null) {

        $leaves = [];
        $leafPath = [];
        if (isset($rootMenuId)) {
            $menuIds = self::subMenuIds($rootMenuId);
        } else {
            $menuIds = self::all()->pluck('id')->toArray();
        }
        /** @var Menu $node */
        foreach ($menuIds as $id) {
            if (empty(self::find($id)->children->count())) {
                $path = self::leafPath($id, $leafPath);
                $leaves[$id] = $path;
                $leafPath = [];
            }
        }

        return $leaves;

    }

    /**
     * 获取指定菜单所有的子菜单Id
     *
     * @param $menuId
     * @return array
     */
    static function subMenuIds($menuId) {

        static $childrenIds;
        $firstIds = Menu::whereParentId($menuId)->get(['id'])->toArray();
        if ($firstIds) {
            foreach ($firstIds as $firstId) {
                $childrenIds[] = $firstId['id'];
                self::subMenuIds($firstId['id']);
            }
        }

        return $childrenIds;

    }

    /**
     * 获取指定菜单的完整路径
     *
     * @param $id
     * @param array $path
     * @return string
     */
    private static function leafPath($id, array &$path) {

        $menu = self::find($id);
        if (!$menu) { return ''; }
        $path[] = $menu->name;
        if (isset($menu->parent_id)) {
            self::leafPath($menu->parent_id, $path);
        }
        krsort($path);

        return implode(' . ', $path);

    }
    
    /**
     * 创建Menu记录, 及卡片绑定记录
     *
     * @param MenuRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    static function store(MenuRequest $request) {

        # 创建新的Menu记录及卡片绑定记录
        try {
            DB::transaction(function () use ($request) {
                $menu = $request->all();
                $menu['position'] = self::all()->count();
                $m = self::create($menu);
                $menuTab = new MenuTab();
                $tabIds = $request->input('tab_ids', []);
                $menuTab->storeByMenuId($m->id, $tabIds);
            });

        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }

    /**
     * 新建菜单记录
     *
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    static function preserve(array $data, $fireEvent = false) {

        $menu = self::create($data);
        if ($menu && $fireEvent) {
            event(new MenuCreated($menu));

            return true;
        }

        return $menu ? true : false;

    }

    /**
     * 更新菜单记录
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool
     */
    static function alter(array $data, $id, $fireEvent = false) {

        $menu = self::find($id);
        if ($menu) {
            $updated = $menu->update($data);
            if ($updated && $fireEvent) {
                event(new MenuUpdated($menu));
                return true;
            }
            return $updated ? true : false;
        }

        return false;

    }
    
    /**
     * 删除菜单记录
     *
     * @param $id
     * @param bool $fireEvent
     * @return bool
     * @throws Throwable
     */
    static function purge($id, $fireEvent = false) {

        $menu = self::find($id);
        if ($menu) {
            try {
                $deleted = $menu->remove($id);
            } catch (Exception $e) {
                throw $e;
            }
            if ($deleted && $fireEvent) {
                event(new MenuDeleted($menu));
                return true;
            }

            return $deleted ? true : false;
        }

        return false;

    }
    
    /**
     * 删除Menu记录
     *
     * @param $menuId
     * @return bool|mixed
     * @throws Throwable
     */
    static function remove($menuId) {

        $menu = self::find($menuId);
        if (!isset($menu)) { return false; }
        try {
            DB::transaction(function () use ($menuId, $menu) {
                # 删除指定的Menu记录
                $menu->delete();
                # 移除指定菜单与卡片的绑定记录
                MenuTab::whereMenuId($menuId)->delete();
                # 删除指定菜单的子所有菜单记录, 以及与卡片的绑定记录
                $subMenus = self::whereParentId($menuId)->get();
                foreach ($subMenus as $subMenu) {
                    self::remove($subMenu->id);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 修改Menu记录
     *
     * @param MenuRequest $request
     * @param $menuId
     * @return bool|mixed
     * @throws Throwable
     */
    static function modify(MenuRequest $request, $menuId) {

        $menu = self::find($menuId);
        if (!isset($menu)) { return false; }
        try {
            DB::transaction(function () use ($request, $menuId, $menu) {
                # 更新指定Menu记录
                $menu->update($request->all());
                # 更新与指定Menu记录绑定的卡片记录
                $menuTab = new MenuTab();
                $menuTab::whereMenuId($menuId)->delete();
                $tabIds = $request->input('tab_ids', []);
                $uri = $request->input('uri', '');
                if (empty($uri)) {
                    if (!empty($tabIds)) {
                        if ($menu->children->count() == 0) {
                            $menuTab->storeByMenuId($menuId, $tabIds);
                        }
                    } else {
                        $menu->update(['enabled' => 0]);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }

    /**
     * 更改菜单所处位置
     *
     * @param $id
     * @param $parentId
     * @param bool $fireEvent
     * @return bool
     */
    static function move($id, $parentId, $fireEvent = false) {

        $menu = self::find($id);
        if (!isset($menu)) { return false; }
        $menu->parent_id = $parentId === '#' ? null : intval($parentId);
        $moved = $menu->save();
        if ($moved && $fireEvent) {
            event(new MenuMoved(self::find($id)));
            return true;
        }

        return $moved ? true : false;

    }

    /**
     * 获取用于显示jstree的菜单数据
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    static function tree($id) {

        $root = self::find($id);
        $menuColor = '<span style="color: %s;">%s</span>';
        $htmlDefaultIcon = '<i class="fa fa-circle-o"></i>';
        $htmlIcon = '<i class="%s"></i>';
        $menus = self::subMenus($id, self::subMenuIds($id), true);
        $menus[$id] = [
            'parent_id' => null,
            'name' => $root->name,
            'icon' => $root->icon ? $root->icon->name : 'fa fa-circle-o',
            'menu_type_id' => $root->menu_type_id,
        ];
        $data = [];
        foreach ($menus as $key => $menu) {
            $name = $menu['name'];
            if (isset($menu['parent_id'])) {
                $icon = $menu['icon'];
                $iconHtml = $icon ? sprintf($htmlIcon, $icon) : $htmlDefaultIcon;
                $name = $iconHtml . '&nbsp;&nbsp;' . $name;
            }
            switch (MenuType::find($menu['menu_type_id'])->name) {
                case '根':
                    $text = sprintf($menuColor, 'darkgray', $name);
                    $type = 'root';
                    break;
                case '运营':
                    $text = sprintf($menuColor, 'darkblue', $name);
                    $type = 'company';
                    break;
                case '企业':
                    $text = sprintf($menuColor, 'darkgreen', $name);
                    $type = 'corp';
                    break;
                case '学校':
                    $text = sprintf($menuColor, 'purple', $name);
                    $type = 'school';
                    break;
                default:
                    $text = $name;
                    $type = 'other';
                    break;
            }
            $parentId = isset($menu['parent_id']) ? $menu['parent_id'] : '#';
            $data[] = [
                'id' => $key,
                'parent' => $parentId,
                'text' => $text,
                'type' => $type,
            ];
        }

        return response()->json($data);

    }

    /**
     * 根据根菜单ID返回其下所有的菜单对象
     *
     * @param $rootId
     * @param $childrenIds
     * @param bool $disabled 是否获取未启用的菜单对象
     * @return Collection|static[]
     */
    private static function subMenus($rootId, $childrenIds = null, $disabled = false) {

        $menus = [];
        $user = Auth::user();
        $role = $user->group->name;

        if ($rootId == 1) {
            $data = self::where('id', '<>', 1)
                ->orderBy('position')
                ->get()->toArray();
        } else {
            if (!in_array($role, self::SUPER_ROLES)) {
                $data = GroupMenu::with('menu')
                    ->where('group_id', $user->group_id)
                    ->get()->pluck('menu')
                    ->toArray();
                $arr = [];
                foreach ($data as $key => $menu) {
                    $arr[$key] = $menu['position'];
                }
                array_multisort($arr, SORT_ASC, $data);
            } else {
                $data = self::whereIn('id', $childrenIds)
                    ->orderBy('position')
                    ->get()->toArray();
            }
        }

        foreach ($data as $datum) {
            $icon = 'fa fa-circle-o';
            if (isset($datum['icon_id'])) {
                $icon = Icon::find($datum['icon_id'])->name;
            }
            if (!$disabled && !$datum['enabled']) { continue; }
            $menus[$datum['id']] = [
                'parent_id' => $datum['parent_id'],
                'name' => $datum['name'],
                'uri' => $datum['uri'],
                'icon' => $icon,
                'menu_type_id' => $datum['menu_type_id'],
            ];
        }

        return $menus;

    }
    
    /**
     * 获取当前登录用户的根菜单ID
     *
     * @param bool $subRoot
     *      false 返回当前角色可访问的最顶级菜单id,
     *      true  返回当前菜单的上级菜单中类型为“学校”、“企业”的id
     * @return int|mixed
     */
    static function rootMenuId($subRoot = false) {

        $user = Auth::user();
        $menuId = session('menuId');
        switch ($user->group->name) {
            case '运营':
                if (!$subRoot) { return 1; };
                $schoolMenuId = self::menuId($menuId);
                if ($schoolMenuId) {
                    return $schoolMenuId;
                } else {
                    $corpMenuId = self::menuId($menuId, '企业');
                    if ($corpMenuId) { return $corpMenuId; }
                }
                return 1;
            case '企业':
                $corpMenuId = self::menuId($menuId, '企业');
                if (!$subRoot) { return $corpMenuId; }
                $schoolMenuId = self::menuId($menuId);
                return $schoolMenuId ?? $corpMenuId;
            case '学校':
                return School::whereDepartmentId($user->topDeptId())
                    ->first()->menu_id;
            default:
                return School::find(Educator::whereUserId($user->id)->first()->school_id)->menu_id;

        }

    }

    /**
     * 获取根节点类型为"学校"的菜单树
     *
     * @param integer $id 指定学校的菜单ID
     * @return \Illuminate\Http\JsonResponse
     */
    static function schoolTree($id) {

        $data = [];
        $menu = self::find($id);
        $data[] = [
            'id' => $menu['id'],
            'parent' => '#',
            'text' => '<i class="fa fa-university"></i>&nbsp;&nbsp;' . $menu['name'],
            'type' => 'school',
        ];
        return response()->json(self::menus($id, $data));

    }

    /**
     * 获取指定学校的所有子菜单
     *
     * @param integer $id
     * @param array $menus
     * @return array
     */
    private static function menus($id, &$menus = []) {

        $htmlDefaultIcon = '<i class="fa fa-circle-o"></i>';
        $htmlIcon = '<i class="%s"></i>';
        $children = self::find($id)->children;

        foreach ($children as $child) {
            $name = $child['name'];
            if (isset($child['parent_id'])) {
                $icon = self::find($child['id'])->icon;
                $iconHtml = $icon ? sprintf($htmlIcon, $icon->name) : $htmlDefaultIcon;
                $name = $iconHtml . '&nbsp;&nbsp;' . $name;
            }
            $menus[] = [
                'id' => $child->id,
                'parent' => $child->parent_id,
                'text' => $name,
                'type' => 'other',
            ];
            self::menus($child->id, $menus);
        }

        return $menus;

    }

    /**
     * 判断当前节点是否可以移动
     *
     * @param $id
     * @param $parentId
     * @return bool
     */
    static function movable($id, $parentId) {

        if (!isset($parentId)) { return false; }
        $type = self::find($id)->menuType->name;
        $parentType = self::find($parentId)->menuType->name;
        switch ($type) {
            case '运营': return $parentType == '根';
            case '企业': return $parentType == '运营';
            case '学校': return $parentType == '企业';
            case '其他': return true;
            default: return false;
        }

    }

    /**
     * 获取后台Menu的HTML字符串
     *
     * @param $rootId
     * @return string
     */
    static function menuHtml($rootId) {

        $menus = self::subMenus($rootId, self::subMenuIds($rootId));
        $menu = self::buildTree($menus, $rootId);

        return substr($menu, 0, -10);

    }

    /**
     * 生成后台菜单Html
     *
     * @param $menus
     * @param $currentParent
     * @param int $currLevel
     * @param int $prevLevel
     * @return string
     */
    private static function buildTree($menus, $currentParent, $currLevel = 0, $prevLevel = -1) {

        static $menuHtml;
        $activeId = session('menuId');
        foreach ($menus as $menuId => $menu) {
            $mId = $menuId;
            $mName = $menu['name'];
            $mIcon = $menu['icon'];
            $mUri = $menu['uri'];
            $hasChildren = self::find($mId)->children->count();
            $mUrl = empty($mUri) ? 'pages/' . $mId : $mUri;
            if ($currentParent == $menu['parent_id']) {
                if ($hasChildren) {
                    $menuHtml .= sprintf(
                        self::TREE,
                        $mId == $activeId ? ' active' : '', $mIcon, $mName
                    );
                } else {
                    $menuHtml .= sprintf(
                        self::SIMPLE,
                        $mId == $activeId ? ' class="active"' : '', $mId, $mUrl, $mIcon, $mName
                    );
                }
                if ($currLevel > $prevLevel) {
                    $prevLevel = $currLevel;
                }
                if ($hasChildren) {
                    $currLevel++;
                    self::buildTree($menus, $menuId, $currLevel, $prevLevel);
                    $currLevel--;
                }

            }
        }
        if ($currLevel == $prevLevel) $menuHtml .= "</ul></li>";

        return $menuHtml;

    }

    /**
     * 根据菜单ID返回其父级菜单中类型为$type的菜单ID
     *
     * @param $id
     * @param string $type
     * @return int|mixed
     */
    static function menuId($id, $type = '学校') {

        $menu = self::find($id);
        $menuType = $menu->menuType->name;
        while ($menuType != $type) {
            $menu = $menu->parent;
            if (!$menu) { return null; }
            $menuType = $menu->menuType->name;
        }

        return $menu->id;

    }

    /**
     * 获取指定菜单所有上级菜单ID
     *
     * @param $menuId
     * @param array $parents
     */
    private static function getParent($menuId, array &$parents) {

        $menu = self::find($menuId);
        if ($menu->parent) {
            $id = $menu->parent->id;
            $parents[] = $id;
            self::getParent($id, $parents);
        }

    }

}

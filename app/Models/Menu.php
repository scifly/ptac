<?php
namespace App\Models;

use App\Events\MenuCreated;
use App\Events\MenuDeleted;
use App\Events\MenuMoved;
use App\Events\MenuUpdated;
use App\Http\Requests\MenuRequest;
use App\Models\MenuTab as MenuTab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\Menu
 *
 * @property int $id
 * @property int|null $parent_id 父菜单ID
 * @property string $name 菜单名称
 * @property int|null $position 菜单所处位置
 * @property string|null $remark 菜单备注
 * @property int $school_id 所属学校ID
 * @property int|null $lft
 * @property int|null $rght
 * @property int|null $media_id 图片ID
 * @property int|null $action_id 对应的控制器action ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Menu whereActionId($value)
 * @method static Builder|Menu whereCreatedAt($value)
 * @method static Builder|Menu whereEnabled($value)
 * @method static Builder|Menu whereId($value)
 * @method static Builder|Menu whereLft($value)
 * @method static Builder|Menu whereMediaId($value)
 * @method static Builder|Menu whereName($value)
 * @method static Builder|Menu whereMenuTypeId($value)
 * @method static Builder|Menu whereParentId($value)
 * @method static Builder|Menu whereRemark($value)
 * @method static Builder|Menu whereRght($value)
 * @method static Builder|Menu whereUpdatedAt($value)
 * @method static Builder|Menu wherePosition($value)
 * @mixin \Eloquent
 * @property-read Collection|\App\Models\Tab[] $tabs
 * @property int|null $icon_id 图标ID
 * @property-read Collection|Menu[] $children
 * @property-read Menu|null $parent
 * @method static Builder|Menu whereIconId($value)
 * @property-read Icon|null $icon
 * @property-read Action|null $action
 * @property-read School $school
 * @property int $menu_type_id 所属菜单类型ID
 * @property-read MenuType $menuType
 * @property-read Company $company
 * @property-read Corp $corp
 * @property-read \App\Models\Media|null $media
 */
class Menu extends Model {

    protected $fillable = [
        'parent_id', 'name', 'remark',
        'menu_type_id', 'position', 'media_id',
        'action_id', 'icon_id', 'enabled',
    ];

    /**
     * 获取菜单所属类型
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function menuType() { return $this->belongsTo('App\Models\MenuType'); }

    /**
     * 返回菜单所属的媒体对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function media() { return $this->belongsTo('App\Models\Media'); }

    /**
     * 获取对应的公司对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function company() { return $this->hasOne('App\Models\Company'); }

    /**
     * 获取对应的企业对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function corp() { return $this->hasOne('App\Models\Corp'); }

    /**
     * 获取对应的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function school() { return $this->hasOne('App\Models\School'); }

    /**
     * 获取菜单包含的卡片
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
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
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() {

        return $this->belongsTo('App\Models\Menu', 'parent_id');

    }

    /**
     * 获取图标
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function icon() {

        return $this->belongsTo('App\Models\Icon');

    }

    /**
     * 获取所有叶节点菜单
     *
     * @param null $rootMenuId
     * @return Collection|static[]
     */
    public function leaves($rootMenuId = null) {

        $leaves = [];
        $leafPath = [];
        if (isset($rootMenuId)) {
            $root = $this->find($rootMenuId);
            $nodes = $root->children();
        } else {
            $nodes = $this->all();
        }
        /** @var Menu $node */
        foreach ($nodes as $node) {
            if (empty($node->children()->count())) {
                $path = $this->leafPath($node->id, $leafPath);
                $leaves[$node->id] = $path;
                $leafPath = [];
            }
        }

        return $leaves;

    }

    /**
     * 获取指定菜单的子菜单
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children() {

        return $this->hasMany('App\Models\Menu', 'parent_id', 'id');

    }

    /**
     * 获取指定菜单的完整路径
     *
     * @param $id
     * @param array $path
     * @return string
     */
    private function leafPath($id, array &$path) {

        $menu = $this->find($id);
        if (!isset($menu)) {
            return '';
        }
        $path[] = $menu->name;
        if (isset($menu->parent_id)) {
            $this->leafPath($menu->parent_id, $path);
        }
        krsort($path);

        return implode(' . ', $path);

    }

    /**
     * 创建Menu记录, 及卡片绑定记录
     *
     * @param MenuRequest $request
     * @return bool|mixed
     */
    public function store(MenuRequest $request) {

        # 创建新的Menu记录及卡片绑定记录
        try {
            $exception = DB::transaction(function () use ($request) {
                $menu = $request->all();
                $menu['position'] = $this->all()->count();
                $m = $this->create($menu);
                $menuTab = new MenuTab();
                $tabIds = $request->input('tab_ids', []);
                $menuTab->storeByMenuId($m->id, $tabIds);
            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 新建菜单记录
     *
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    public function preserve(array $data, $fireEvent = false) {

        $menu = $this->create($data);
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
    public function alter(array $data, $id, $fireEvent = false) {

        $menu = $this->find($id);
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
     */
    public function purge($id, $fireEvent = false) {

        $menu = $this->find($id);
        if ($menu) {
            $deleted = $menu->remove($id);
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
     */
    public function remove($menuId) {

        $menu = $this->find($menuId);
        if (!isset($menu)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($menuId, $menu) {
                # 删除指定的Menu记录
                $menu->delete();
                # 移除指定菜单与卡片的绑定记录
                MenuTab::whereMenuId($menuId)->delete();
                # 删除指定菜单的子所有菜单记录, 以及与卡片的绑定记录
                $subMenus = $this->where('parent_id', $menuId)->get();
                foreach ($subMenus as $subMenu) {
                    $this->remove($subMenu->id);
                }

            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 修改Menu记录
     *
     * @param MenuRequest $request
     * @param $menuId
     * @return bool|mixed
     */
    public function modify(MenuRequest $request, $menuId) {

        $menu = $this->find($menuId);
        if (!isset($menu)) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($request, $menuId, $menu) {
                # 更新指定Menu记录
                $menu->update($request->all());
                # 更新与指定Menu记录绑定的卡片记录
                $tabIds = $request->input('tab_ids', []);
                $menuTab = new MenuTab();
                $menuTab::whereMenuId($menuId)->delete();
                $menuTab->storeByMenuId($menuId, $tabIds);
                /*foreach ($tabIds as $tabId) {
                    $menuTab->create([
                        'menu_id' => $menuId,
                        'tab_id' => $tabId,
                        'enabled' => 1
                    ]);
                }*/
            });

            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }

    }

    /**
     * 更改菜单所处位置
     *
     * @param $id
     * @param $parentId
     * @param bool $fireEvent
     * @return bool
     */
    public function move($id, $parentId, $fireEvent = false) {

        $menu = $this->find($id);
        if (!isset($menu)) {
            return false;
        }
        $menu->parent_id = $parentId === '#' ? null : intval($parentId);
        $moved = $menu->save();
        if ($moved && $fireEvent) {
            event(new MenuMoved($this->find($id)));

            return true;
        }

        return $moved ? true : false;

    }

    /**
     * 获取用于显示jstree的菜单数据
     *
     * @param null $rootMenuId
     * @param null $rootType
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree($rootMenuId = null, $rootType = null) {

        $fields = ['id', 'parent_id', 'name', 'position', 'menu_type_id'];
        $menuColor = '<span style="color: %s;">%s</span>';
        $htmlDefaultIcon = '<i class="fa fa-circle-o"></i>';
        $htmlIcon = '<i class="%s"></i>';
        if (isset($rootMenuId)) {
            switch ($rootType)
            {
                # 说明是运营管理员
                case '根':
                    $menus = $this->get($fields)->sortBy(['position'])->toArray();
                    break;
                default:
                    $rootMenu = $this->find($rootMenuId);
                    $childrenId = $this->getChildren($rootMenuId);
                    $menus = $this->whereIn('id',$childrenId)->get($fields)->sortBy(['position'])->toArray();
                    foreach ($menus as $key => $v) {
                        $menus[$key + 1] = $v;
                    }
                    $menus[0] = [
                        'id'           => $rootMenu->id,
                        'parent_id'    => null,
                        'name'         => $rootMenu->name,
                        'position'     => $rootMenu->position,
                        'menu_type_id' => $rootMenu->menu_type_id,
                    ];
                 break;

            }
        } else {
            $menus = $this->get($fields)->sortBy(['position'])->toArray();
        }
        $data = [];
        foreach ($menus as $menu) {
            $name = $menu['name'];
            if (isset($menu['parent_id'])) {
                $icon = $this->find($menu['id'])->icon;
                $iconHtml = $icon ? sprintf($htmlIcon, $icon->name) : $htmlDefaultIcon;
                $name = $iconHtml . '&nbsp;&nbsp;' . $name;
            }
            switch (MenuType::whereId($menu['menu_type_id'])->first()->name) {
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
                'id'     => $menu['id'],
                'parent' => $parentId,
                'text'   => $text,
                'type'   => $type,
            ];
        }

        return response()->json($data);

    }

    private function menus($id, &$menus = []) {
        $htmlDefaultIcon = '<i class="fa fa-circle-o"></i>';
        $htmlIcon = '<i class="%s"></i>';
        $children = $this->find($id)->children;
        foreach ($children as $child) {
            $name = $child['name'];
            if (isset($child['parent_id'])) {
                $icon = $this->find($child['id'])->icon;
                $iconHtml = $icon ? sprintf($htmlIcon, $icon->name) : $htmlDefaultIcon;
                $name = $iconHtml . '&nbsp;&nbsp;' . $name;
            }
            $menus[] = [
                'id' => $child->id,
                'parent' => $child->parent_id,
                'text' => $name,
                'type' => 'other'
            ];
            $this->menus($child->id, $menus);
        }
        return $menus;

    }


    public function getTreeByMenuId($id) {

        $data = [];
        $menu = $this->find($id);
        $data[] = [
            'id'     => $menu['id'],
            'parent' => '#',
            'text'   => '<i class="fa fa-university"></i>&nbsp;&nbsp;' . $menu['name'],
            'type'   => 'school'
        ];
        return response()->json($this->menus($id, $data));

    }

    public function movable($id, $parentId) {

        if (!isset($parentId)) { return false; }
        $type = $this->find($id)->menuType->name;
        $parentType = $this->find($parentId)->menuType->name;
        switch ($type) {
            case '运营':
                return $parentType == '根';
            case '企业':
                return $parentType == '运营';
            case '学校':
                return $parentType == '企业';
            case '其他':
                return true;
            default:
                return false;
        }

    }

    /**
     * 获取后台Menu的HTML字符串
     *
     * @param $activeMenuId
     * @return string
     */
    public function getMenuHtml($activeMenuId) {

        $parents = [$activeMenuId];
        $this->getParent($activeMenuId, $parents);

        $id = '';
        if ($activeMenuId>=3) {
            $childrenId = $this->getChildren($activeMenuId);
            $id = array_unique($childrenId);
        }

        # 不含子菜单的HTML模板
        $mSimple = '<li%s><a id="%s" href="%s"><i class="%s"></i> %s</a></li>';
        # 包含子菜单的HTML模板
        $mTree = <<<HTML
            <li class="treeview%s">
                <a href="#">
                    <i class="%s"></i> <span>%s</span>
                    <span class="pull-right-container">
                        <i class="fa fa-angle-left pull-right"></i>
                    </span>
                </a>
                <ul class="treeview-menu">
HTML;
        # ul列表尾部
        $mEnd = '</ul></li>';
        # 获取指定学校所有已启用的菜单对象
        // $menus = $this->where('enabled', 1)
        //     ->where('id', '<>', 1)
        //     ->orderBy('position')->get();
        if(isset($id)&& !empty($id))
        {
            if ($id>3) {
                $menus = $this->whereIn('id', $id)
                    ->where('id', '<>', 1)
                    ->orderBy('position')->get();
            } else {
                $menus = $this->where('enabled', 1)
                    ->where('id', '<>', 1)
                    ->orderBy('position')->get();
            }
        }else{
            $menus = $this->where('enabled', 1)
                ->where('id', '<>', 1)
                ->orderBy('position')->get();
        }

        $menu = '';
        $level = 1;
        $parentId = 1;
        foreach ($menus as $k => $m) {
            # 获取菜单ID
            $mId = $m->id;
            $isActive = in_array($mId, $parents);
            # 获取菜单名称
            $mName = $m->name;
            /** @noinspection PhpUndefinedMethodInspection */
            $hasChildren = $m->children->count();
            # 获取菜单对应的Icon
            $mIcon = $m->icon_id ? $m->icon->name : 'fa fa-circle-o';
            # 获取菜单对应的默认Route
            // $mUrl = $m->action_id ? '/urlshortener/public/' . $m->action->route : '#';
            // $mUrl = $mUrl ? $mUrl : '#';
            $mUrl = '../pages/' . $m->id;
            # 计算菜单所处的level
            $lvl = 0;
            $this->getMenuLevel($m, $lvl);
            $mLevel = $lvl;
            if ($m->parent_id == $parentId) {
                if ($hasChildren) {
                    $level += 1;
                    $menu .= sprintf(
                        $mTree,
                        $isActive ? ' active' : '',
                        $mIcon,
                        $mName
                    );
                    $parentId = $m->id;
                } else {
                    $menu .= sprintf(
                        $mSimple,
                        $isActive ? ' class="active"' : '',
                        $mId,
                        $mUrl,
                        $mIcon,
                        $mName
                    );
                }
            } else {
                $parentId = $m->parent_id;
                $level = $level - $mLevel;
                for ($i = 0; $i < $level; $i++) {
                    $menu .= $mEnd;
                }
                $level = $mLevel;
                if ($hasChildren) {
                    $level += 1;
                    $menu .= sprintf(
                        $mTree,
                        $isActive ? ' active' : '',
                        $mIcon,
                        $mName
                    );
                    $parentId = $m->id;
                } else {
                    $menu .= sprintf(
                        $mSimple,
                        $isActive ? ' class="active"' : '',
                        $mId,
                        $mUrl,
                        $mIcon,
                        $mName
                    );
                }
            }

        }
        if ($level > 1) {
            for ($i = 0; $i < $level - 1; $i++) {
                $menu .= $mEnd;
            }
        }

        return $menu;

    }

    /**
     * 获取指定菜单所有的子菜单Id
     *
     * @param $menuId
     * @return array
     */
    private function getChildren($menuId) {
        static $childIds;
        $firstIds = Menu::where('parent_id', $menuId)->get(['id'])->toArray();
        if ($firstIds) {
            foreach ($firstIds as $firstId) {
                $childIds[] = $firstId['id'];
                $this->getChildren($firstId['id']);
            }
        }

        return $childIds;
    }

    /**
     * 计算指定菜单所处的level
     *
     * @param Menu $m
     * @param $lvl
     */
    private function getMenuLevel(Menu $m, &$lvl) {

        if ($m->parent) {
            $lvl += 1;
            $this->getMenuLevel($m->parent, $lvl);
        }

    }

    /**
     * 获取后台菜单数组
     *
     * @param mixed $records 所有菜单对象
     * @param array $menus 菜单数组
     */
    public function getMenuArray($records, &$menus) {

        if (isset($records)) {
            $i = 0;
            foreach ($records as $r) {
                if (isset($r->action_id)) {
                    $action = Action::whereId($r->action_id)->get()[0];
                    $r->action = $action->toArray();
                }
                if (isset($r->icon_id)) {
                    $icon = Icon::whereId($r->icon_id)->get()[0];
                    $r->icon = $icon->toArray();
                }
                /** @noinspection PhpUndefinedMethodInspection */
                $menus[] = $r->toArray();
                $children = $r->children;
                /** @noinspection PhpUndefinedMethodInspection */
                $this->getMenus($children->sortBy('position'), $menus[$i]['children']);
                $i++;
            }
        }

    }

    /**
     * 获取指定菜单所有上级菜单ID
     *
     * @param $menuId
     * @param array $parents
     */
    private function getParent($menuId, array &$parents) {

        $menu = $this->find($menuId);
        if ($menu->parent) {
            $id = $menu->parent->id;
            $parents[] = $id;
            $this->getParent($id, $parents);
        }

    }

}

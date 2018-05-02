<?php
namespace App\Models;

use App\Events\MenuCreated;
use App\Events\MenuDeleted;
use App\Events\MenuUpdated;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\MenuRequest;
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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use ReflectionException;
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
 * @property-read Collection|GroupMenu[] $groupMenus
 */
class Menu extends Model {
    
    use ModelTrait;
    // todo: needs to be optimized
    protected $fillable = [
        'parent_id', 'name', 'remark', 'uri',
        'menu_type_id', 'position', 'media_id',
        'action_id', 'icon_id', 'enabled',
    ];
    
    protected $menuTab, $mt, $department;
    
    function __construct(array $attributes = [], MenuTab $menuTab, MenuType $mt, Department $department) {
        
        $this->menuTab = $menuTab;
        $this->mt = $mt;
        $this->department = $department;
        parent::__construct($attributes);
        
        
    }
    
    /**
     * 获取菜单所属类型
     *
     * @return BelongsTo
     */
    function menuType() { return $this->belongsTo('App\Models\MenuType'); }
    
    /**
     * 返回菜单所属的媒体对象
     *
     * @return BelongsTo
     */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /**
     * 获取对应的公司对象
     *
     * @return HasOne
     */
    function company() { return $this->hasOne('App\Models\Company'); }
    
    /**
     * 获取对应的企业对象
     *
     * @return HasOne
     */
    function corp() { return $this->hasOne('App\Models\Corp'); }
    
    /**
     * 获取对应的学校对象
     *
     * @return HasOne
     */
    function school() { return $this->hasOne('App\Models\School'); }
    
    /**
     * 获取指定菜单所属的所有角色对象
     *
     * @return BelongsToMany
     */
    function groups() { return $this->belongsToMany('App\Models\Group', 'groups_menus'); }
    
    /**
     * 获取指定菜单包含的所有角色菜单对象
     *
     * @return HasMany
     */
    function groupMenus() { return $this->hasMany('App\Models\GroupMenu'); }
    
    /**
     * 获取菜单包含的卡片
     *
     * @return BelongsToMany
     */
    function tabs() {
        
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
    function parent() { return $this->belongsTo('App\Models\Menu', 'parent_id'); }
    
    /**
     * 获取图标
     *
     * @return BelongsTo
     */
    function icon() { return $this->belongsTo('App\Models\Icon'); }
    
    /**
     * 获取指定菜单的子菜单
     *
     * @return HasMany
     */
    function children() { return $this->hasMany('App\Models\Menu', 'parent_id', 'id'); }
    
    /**
     * 获取所有叶节点菜单
     *
     * @param null $rootMenuId
     * @return Collection|static[]
     */
    function leaves($rootMenuId = null) {
        
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
    function subMenuIds($menuId) {
        
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
     * 创建Menu记录, 及卡片绑定记录
     *
     * @param MenuRequest $request
     * @return bool|mixed
     * @throws Exception
     * @throws Throwable
     */
    function store(MenuRequest $request) {
        
        $menu = null;
        # 创建新的Menu记录及卡片绑定记录
        try {
            DB::transaction(function () use ($request, &$menu) {
                $data = $request->all();
                $data['position'] = $this->all()->max('position') + 1;
                $menu = $this->create($menu);
                $tabIds = $request->input('tab_ids', []);
                $menu->menuType->storeByMenuId($menu->id, $tabIds);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $menu;
        
    }
    
    /**
     * 创建并返回指定（运营/企业/学校）对应的菜单对象
     *
     * @param Model $model
     * @param null $beLongsTo
     * @return $this|Model
     * @throws ReflectionException
     */
    function storeMenu(Model $model, $beLongsTo = null) {
    
        list($iconId, $mtId) = $this->mt->mtIds($model);

        return $this->create([
            'parent_id' => $beLongsTo
                ? $model->{$beLongsTo}->menu_id
                : $this->where('parent_id', null)->first()->id,
            'name' => $model->{'name'},
            'remark' => $model->{'remark'},
            'menu_type_id' => $mtId,
            'icon_id' => $iconId,
            'position' => $this->all()->max('position') + 1,
            'enabled' => $model->{'enabled'}
        ]);
    
    }
    
    /**
     * 新建菜单记录
     *
     * @param array $data
     * @param bool $fireEvent
     * @return bool
     */
    function preserve(array $data, $fireEvent = false) {
        
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
    function alter(array $data, $id, $fireEvent = false) {
        
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
    function purge($id, $fireEvent = false) {
        
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
     * 修改Menu记录
     *
     * @param MenuRequest $request
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function modify(MenuRequest $request, $id) {
        
        $menu = null;
        try {
            DB::transaction(function () use ($request, $id, &$menu) {
                $menu = $this->find($id);
                # 更新指定Menu记录
                $menu->update($request->all());
                # 更新与指定Menu记录绑定的卡片记录
                $this->menuTab::whereMenuId($id)->delete();
                $tabIds = $request->input('tab_ids', []);
                $uri = $request->input('uri', '');
                if (empty($uri)) {
                    if (!empty($tabIds)) {
                        if ($menu->children->count() == 0) {
                            $this->menuTab->storeByMenuId($id, $tabIds);
                        }
                    } else {
                        $menu->update(['enabled' => 0]);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $menu ? $this->find($id) : null;
        
    }
    
    /**
     * 更新(运营/企业/学校)对应的菜单
     *
     * @param Model $model
     * @param null $belongsTo
     * @return bool
     */
    function modifyMenu(Model $model, $belongsTo = null) {
        
        return $this->update([
            'name' => $model->{'name'},
            'remark' => $model->{'remark'},
            'parent_id' => $belongsTo
                ? $this->{$belongsTo}->deparmtent_id
                : $this::whereParentId(null)->first()->id,
            'enabled' => $model->{'enabled'}
        ]);
        
    }
    
    /**
     * 删除Menu记录
     *
     * @param $menuId
     * @return bool|mixed
     * @throws Throwable
     */
    function remove($menuId) {
        
        $menu = self::find($menuId);
        
        return $this->removable($menu)
            ? $menu->delete()
            : false;
        
    }
    
    /**
     * 移除(运营/企业/学校)对应的菜单
     *
     * @param Model $model
     * @return bool|mixed
     * @throws Throwable
     */
    function removeMenu(Model $model) {
        
        return $this->remove(
            $this->find($model->{'menu_id'})->id
        );
        
    }
    
    /**
     * 菜单列表(菜单移动、排序)
     *
     * @param null $id
     * @param null $parentId
     * @return bool|JsonResponse
     * @throws Exception
     */
    function index($id = null, $parentId = null) {
    
        if (Request::has('id')) {
            return $this->tree(
                $this->rootMenuId(true)
            );
        } else if (Request::has('data')) {
            # 保存菜单排序
            $positions = Request::get('data');
            foreach ($positions as $id => $pos) {
                $menu = $this->find($id);
                if (isset($menu)) {
                    $menu->position = $pos;
                    $menu->save();
                }
            }
        } else {
            # 移动菜单
            abort_if(
                !$this->find($id) || !$this->find($parentId),
                HttpStatusCode::NOT_FOUND,
                __('messages.not_found')
            );
            if ($this->movable($id, $parentId)) {
                $moved = $this->move($id, $parentId);
                abort_if(
                    !$moved,
                    HttpStatusCode::BAD_REQUEST,
                    __('messages.bad_request')
                );
            }
        }
        
        return '';
    
    }
    
    /**
     * 更改菜单所处位置
     *
     * @param $id
     * @param $parentId
     * @return bool
     * @throws Exception
     */
    private function move($id, $parentId) {
        
        $moved = false;
        try {
            DB::transaction(function () use ($id, $parentId, &$moved) {
                $menu = $this->find($id);
                if (!isset($menu)) {
                    $moved = false;
                } else {
                    $menu->parent_id = $parentId === '#' ? null : intval($parentId);
                    $moved = $menu->save();
                    /** 当企业类菜单所属运营类菜单发生变化时，更新企业所属运营者及所属部门 */
                    if ($moved && $menu->menuType->name == '企业') {
                        $corp = Corp::whereMenuId($menu->id)->first();
                        $company = Company::whereMenuId($menu->parent_id)->first();
                        $data = ['parent_id' => $company->department_id];
                        $moved = $moved && ($this->department->modify($data, $corp->department_id) ? true : false);
                        $data = ['company_id' => $company->id];
                        $moved = $moved && $corp->update($data);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return $moved;
        
    }
    
    /**
     * 获取用于显示jstree的菜单数据
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    function tree($id) {
        
        $root = $this->find($id);
        $menus = $this->subMenus(
            $id, self::subMenuIds($id), true
        );
        $menus[$id] = [
            'parent_id'    => null,
            'name'         => $root->name,
            'icon'         => $root->icon ? $root->icon->name : 'fa fa-circle-o',
            'menu_type_id' => $root->menu_type_id,
            'enabled'      => 1,
        ];
        $tree = [];
        foreach ($menus as $key => $menu) {
            $name = $menu['name'];
            if (isset($menu['parent_id'])) {
                $icon = $menu['icon'];
                $iconHtml = $icon
                    ? sprintf(Snippet::MENU_ICON, $icon)
                    : Snippet::MENU_DEFAULT_ICON;
                $name = $iconHtml . '&nbsp;&nbsp;' . $name;
            }
            $parentId = isset($menu['parent_id']) ? $menu['parent_id'] : '#';
            $menuType = MenuType::find($menu['menu_type_id'])->name;
            $type = Constant::NODE_TYPES[$menuType]['type'];
            $color = Constant::NODE_TYPES[$menuType]['color'];
            $text = sprintf(
                Snippet::NODE_TEXT,
                $menu['enabled'] ? $color : 'text-gray',
                $name
            );
            switch ($type) {
                case '企业':
                    $corp_id = Corp::whereMenuId($key)->first()->id;
                    break;
                case '学校':
                    $corp_id = School::whereMenuId($key)->first()->corp_id;
                    break;
                default:
                    $corp_id = null;
                    break;
            }
            $tree[] = [
                'id'     => $key,
                'parent' => $parentId,
                'text'   => $text,
                'type'   => $type,
                'corp_id' => $corp_id
            ];
        }
    
        return response()->json($tree);
        
    }
    
    /**
     * 获取当前登录用户的顶级菜单ID
     *
     * @param bool $subRoot
     *      false 返回当前角色可访问的最顶级菜单id,
     *      true  返回当前角色可访问的最直接顶级(学校或企业)菜单id
     * @return int|mixed
     */
    function rootMenuId($subRoot = false) {
        
        $user = Auth::user();
        $rootMTId = MenuType::whereName('根')->first()->id;
        $rootMId = Menu::whereMenuTypeId($rootMTId)->first()->id;
        $menuId = session('menuId') ?? $rootMId;
        $smId = self::menuId($menuId);
        $cmId = self::menuId($menuId, '企业');
        switch ($user->group->name) {
            case '运营':
                return !$subRoot ? $rootMId : ($smId ?? ($cmId ?? $rootMId));
            case '企业':
                $departmentId = $this->head($user);
                $cmId = $cmId ?? Corp::whereDepartmentId($departmentId)->first()->menu_id;
                return !$subRoot ? $cmId : ($smId ?? $cmId);
            case '学校':
                $departmentId = $this->head($user);
                return $smId ?? School::whereDepartmentId($departmentId)->first()->menu_id;
            default:
                return School::find($user->educator->school_id)->menu_id;
        }
        
    }
    
    /**
     * 获取根节点类型为"学校"的菜单树
     *
     * @param integer $id 指定学校的菜单ID
     * @return JsonResponse
     */
    function schoolTree($id) {
        
        $data = [];
        $menu = self::find($id);
        $data[] = [
            'id'     => $menu['id'],
            'parent' => '#',
            'text'   => '<i class="fa fa-university"></i>&nbsp;&nbsp;' . $menu['name'],
            'type'   => 'school',
        ];
        
        return response()->json(self::menus($id, $data));
        
    }
    
    /**
     * 判断当前节点是否可以移动
     *
     * @param $id
     * @param $parentId
     * @return bool
     */
    function movable($id, $parentId) {
        
        if (!isset($id, $parentId)) {
            return false;
        }
        $user = Auth::user();
        $role = $user->group->name;
        if ($role != '运营') {
            $menuIds = $this->menuIds($this);
            abort_if(
                !in_array($id, $menuIds) || !in_array($parentId, $menuIds),
                HttpStatusCode::UNAUTHORIZED,
                __('messages.forbidden')
            );
        }
        $type = $this->find($id)->menuType->name;
        $parentType = $this->find($parentId)->menuType->name;
        switch ($type) {
            case '运营':
                return $parentType == '根';
            case '企业':
                return $parentType == '运营';
            case '学校':
                $school = School::whereMenuId($id)->first();
                if ($parentType !== '企业') {
                    return false;
                } else {
                    $corp = Corp::whereMenuId($parentId)->first();
                    # 如果学校所属企业发生变化，则不允许移动
                    return $school->corp_id == $corp->id;
                }
            case '其他':
                return true;
            default:
                return false;
        }
        
    }
    
    /**
     * 获取后台Menu的HTML字符串
     *
     * @param $rootId
     * @return string
     */
    function menuHtml($rootId) {
        
        $menus = self::subMenus($rootId, self::subMenuIds($rootId));
        $menu = self::buildTree($menus, $rootId);
        
        return $menu; // substr($menu, 0, -10);
    }
    
    /**
     * 根据菜单ID返回其父级菜单中类型为$type的菜单ID
     *
     * @param $id
     * @param string $type
     * @return int|mixed
     */
    function menuId($id, $type = '学校') {
        
        $menu = self::find($id);
        $menuType = $menu->menuType->name;
        while ($menuType != $type) {
            $menu = $menu->parent;
            if (!$menu) {
                return null;
            }
            $menuType = $menu->menuType->name;
        }
        
        return $menu->id;
        
    }
    
    /**
     * 获取指定菜单对应的部门图标及名称
     *
     * @param $id
     * @return array
     */
    function department($id) {
        
        $icon = 'fa fa-send-o text-blue';
        $name = '家校通运营';
        $departmentMenuId = $this->menuId($id);
        
        if ($departmentMenuId) {
            $icon = 'fa fa-university text-purple';
            $name = $this->find($departmentMenuId)->name;
        } else {
            $departmentMenuId = $this->menuId($id, '企业');
            if ($departmentMenuId) {
                $icon = 'fa fa-weixin text-green';
                $name = $this->find($departmentMenuId)->name;
            }
        }
        
        return ['icon' => $icon, 'name' => $name];
        
    }
    
    /**
     * 获取指定菜单的完整路径
     *
     * @param $id
     * @param array $path
     * @return string
     */
    private function leafPath($id, array &$path) {
        
        $menu = self::find($id);
        if (!$menu) {
            return '';
        }
        $path[] = $menu->name;
        if (isset($menu->parent_id)) {
            self::leafPath($menu->parent_id, $path);
        }
        krsort($path);
        
        return implode(' . ', $path);
        
    }
    
    /**
     * 根据根菜单ID返回其下所有的菜单对象
     *
     * @param $rootId
     * @param $childrenIds
     * @param bool $disabled 是否获取未启用的菜单对象
     * @return Collection|static[]
     */
    private function subMenus($rootId, $childrenIds = null, $disabled = false) {
        
        $menus = [];
        $user = Auth::user();
        $role = $user->group->name;
        if ($rootId == 1) {
            $data = self::where('id', '<>', 1)
                ->orderBy('position')
                ->get()->toArray();
        } else {
            if (!in_array($role, Constant::SUPER_ROLES)) {
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
            if (!$disabled && !$datum['enabled']) {
                continue;
            }
            $menus[$datum['id']] = [
                'parent_id'    => $datum['parent_id'],
                'name'         => $datum['name'],
                'uri'          => $datum['uri'],
                'icon'         => $icon,
                'menu_type_id' => $datum['menu_type_id'],
                'enabled'      => $datum['enabled'],
            ];
        }
        
        return $menus;
        
    }
    
    /**
     * 获取指定学校的所有子菜单
     *
     * @param integer $id
     * @param array $menus
     * @return array
     */
    private function menus($id, &$menus = []) {
        
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
                'id'     => $child->id,
                'parent' => $child->parent_id,
                'text'   => $name,
                'type'   => 'other',
            ];
            self::menus($child->id, $menus);
        }
        
        return $menus;
        
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
    private function buildTree($menus, $currentParent, $currLevel = 0, $prevLevel = -1) {
        
        static $menuHtml;
        $activeId = session('menuId');
        foreach ($menus as $menuId => $menu) {
            $mId = $menuId;
            $mName = $menu['name'];
            $mIcon = $menu['icon'];
            $mUri = $menu['uri'];
            // $hasChildren = self::find($mId)->children->count();
            $hasChildren = self::whereParentId($mId)->where('enabled', 1)->get()->count();
            $mUrl = empty($mUri) ? 'pages/' . $mId : $mUri;
            if ($currentParent == $menu['parent_id']) {
                if ($hasChildren) {
                    $menuHtml .= sprintf(
                        Snippet::TREE,
                        $mId == $activeId ? ' active' : '', $mIcon, $mName
                    );
                } else {
                    $menuHtml .= sprintf(
                        Snippet::SIMPLE,
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
     * 获取指定菜单所有上级菜单ID
     *
     * @param $menuId
     * @param array $parents
     */
    private function getParent($menuId, array &$parents) {
        
        $menu = self::find($menuId);
        if ($menu->parent) {
            $id = $menu->parent->id;
            $parents[] = $id;
            self::getParent($id, $parents);
        }
        
    }
    
}

<?php
namespace App\Models;

use App\Helpers\{Constant, HttpStatusCode, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Html;
use Illuminate\Database\Eloquent\{Builder,
    Collection,
    Model,
    Relations\BelongsTo,
    Relations\BelongsToMany,
    Relations\HasMany,
    Relations\HasOne};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{Auth, DB, Request};
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
 * @property-read MenuType $mType
 * @property-read Menu|null $parent
 * @property-read School $school
 * @property-read Collection|Tab[] $tabs
 * @property-read Collection|GroupMenu[] $groupMenus
 * @property-read int|null $children_count
 * @property-read int|null $groups_count
 * @property-read int|null $tabs_count
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
 * @method static Builder|Menu newModelQuery()
 * @method static Builder|Menu newQuery()
 * @method static Builder|Menu query()
 * @mixin Eloquent
 */
class Menu extends Model {
    
    use ModelTrait;
    // todo: needs to be optimized
    protected $fillable = [
        'parent_id', 'name', 'remark', 'uri',
        'menu_type_id', 'position', 'media_id',
        'action_id', 'icon_id', 'enabled',
    ];
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
    const /** @noinspection HtmlUnknownTarget */
        SIMPLE = '<li%s><a id="%s" href="%s" class="leaf"><i class="%s"></i> <span>%s</span></a></li>';
    
    /** properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function mType() { return $this->belongsTo('App\Models\MenuType', 'message_type_id'); }
    
    /** @return BelongsTo */
    function media() { return $this->belongsTo('App\Models\Media'); }
    
    /** @return HasOne */
    function company() { return $this->hasOne('App\Models\Company'); }
    
    /** @return HasOne */
    function corp() { return $this->hasOne('App\Models\Corp'); }
    
    /** @return HasOne */
    function school() { return $this->hasOne('App\Models\School'); }
    
    /** @return BelongsToMany */
    function groups() { return $this->belongsToMany('App\Models\Group', 'group_menu'); }
    
    /** @return BelongsToMany */
    function tabs() { return $this->belongsToMany('App\Models\Tab', 'menu_tab'); }
    
    /** @return BelongsTo */
    function parent() { return $this->belongsTo('App\Models\Menu', 'parent_id'); }
    
    /** @return BelongsTo */
    function icon() { return $this->belongsTo('App\Models\Icon'); }
    
    /** @return HasMany */
    function children() { return $this->hasMany('App\Models\Menu', 'parent_id', 'id'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function index() {
        
        $response = response()->json();
        switch (Request::input('action')) {
            case 'tree':
                $response = $this->jsTree($this->rootId(true));
                break;
            case 'sort':
                # 保存菜单排序
                $positions = Request::get('data');
                $originalPositions = $this->orderBy('position')
                    ->whereIn('id', array_keys($positions))
                    ->pluck('position', 'id')->toArray();
                foreach ($positions as $id => $position) {
                    $originalPosition = array_slice(
                        $originalPositions, $position, 1, true
                    );
                    $this->find($id)->update([
                        'position' => $originalPosition[key($originalPosition)],
                    ]);
                }
                break;
            case 'move':
                $id = Request::input('id');
                $parentId = Request::input('parentId');
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
                break;
            default:
                break;
        }
        
        return $response;
        
    }
    
    /**
     * @param array $data
     * @return bool|mixed
     * @throws Throwable
     */
    function store(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                $data['position'] = $this->all()->max('position') + 1;
                $menu = $this->create($data);
                $tabIds = $data['tab_ids'] ?? [];
                (new MenuTab)->store($menu->id, $tabIds);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 创建非'其他'类型菜单
     *
     * @param Model $model
     * @param null $beLongsTo
     * @return $this|Model
     */
    function stow(Model $model, $beLongsTo = null) {

        return $this->create([
            'parent_id'    => $beLongsTo
                ? $model->{$beLongsTo}->menu_id
                : $this->where('parent_id', null)->first()->id,
            'name'         => $model->{'name'},
            'remark'       => $model->{'remark'},
            'menu_type_id' => ($menuType = MenuType::whereRemark(lcfirst(class_basename($model)))->first())->id,
            'icon_id'      => Icon::whereName($menuType->icon)->first()->id,
            'position'     => $this->all()->max('position') + 1,
            'enabled'      => $model->{'enabled'},
        ]);
        
    }
    
    /**
     * @param array $data
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        try {
            DB::transaction(function () use ($data, $id) {
                $menu = $this->find($id);
                # 更新指定Menu记录
                $menu->update($data);
                # 更新与指定Menu记录绑定的卡片记录
                $menuTab = new MenuTab();
                $menuTab::whereMenuId($id)->delete();
                $tabIds = $data['tab_ids'] ?? [];
                $uri = $data['uri'] ?? '';
                if (empty($uri)) {
                    if (!empty($tabIds)) {
                        $menu->children->count() ?: $menuTab->store($id, $tabIds);
                    } else {
                        $enabledSubMenus = $menu->children->filter(
                            function (Menu $menu) { return $menu->enabled; }
                        );
                        $menu->update(['enabled' => $enabledSubMenus->count() ? 1 : 0]);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 更新非'其他'类型菜单
     *
     * @param Model $model
     * @param null $belongsTo
     * @return bool
     */
    function alter(Model $model, $belongsTo = null) {
        
        return $this->find($model->{'menu_id'})->update([
            'name'      => $model->{'name'},
            'remark'    => $model->{'remark'},
            'parent_id' => $belongsTo
                ? $model->{$belongsTo}->menu_id
                : $this::whereParentId(null)->first()->id,
            'enabled'   => $model->{'enabled'},
        ]);
        
    }
    
    /**
     * @param $id
     * @return bool|mixed
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $menuIds = [];
                foreach ($ids as $id) {
                    $menuIds = array_merge($menuIds, [$id], $this->subIds($id));
                }
                $ids = array_unique($menuIds);
                array_map(
                    function ($class, $field) use ($ids) {
                        $this->model($class)->whereIn($field, $ids)->delete();
                    }, ['GroupMenu', 'MenuTab', 'Menu'], ['menu_id', 'menu_id', 'id']
                );
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 获取指定菜单所有的子菜单Id
     *
     * @param $id
     * @return array
     */
    function subIds($id) {
        
        static $subIds;
        $childrenIds = Menu::whereParentId($id)->pluck('id')->toArray();
        foreach ($childrenIds as $childId) {
            $subIds[] = $childId;
            $this->subIds($childId);
        }
        
        return $subIds ?? [];
        
    }
    
    /**
     * 获取当前登录用户的顶级菜单ID
     *
     * @param bool $subRoot
     *      false 返回当前角色可访问的最顶级菜单id,
     *      true  返回当前角色可访问的最直接顶级(学校或企业)菜单id
     * @return int|mixed
     */
    function rootId($subRoot = false) {
        
        $user = Auth::user();
        $role = $user->role();
        $rootMTId = MenuType::whereName('根')->first()->id;
        $rootMId = Menu::whereMenuTypeId($rootMTId)->first()->id;
        $menuId = session('menuId') != '0' ? session('menuId') : $rootMId;
        $smId = $this->menuId($menuId);
        $cmId = $this->menuId($menuId, '企业');
        $deptId = in_array($role, Constant::SUPER_ROLES) ? $user->depts->first()->id : null;
        switch ($role) {
            case '运营':
                return !$subRoot ? $rootMId : ($smId ?? ($cmId ?? $rootMId));
            case '企业':
                $cmId = $cmId ?? Corp::whereDepartmentId($deptId)->first()->menu_id;
                
                return !$subRoot ? $cmId : ($smId ?? $cmId);
            case '学校':
                return $smId ?? School::whereDepartmentId($deptId)->first()->menu_id;
            default:
                return School::find($user->educator->school_id)->menu_id;
        }
        
    }
    
    /**
     * 根据菜单ID返回其父级菜单中类型为$type的菜单ID
     *
     * @param $id
     * @param string $type
     * @return int|mixed
     */
    function menuId($id, $type = '学校') {
        
        if (!$menu = $this->find($id)) return null;
        $mtName = $menu->mType->name;
        while ($mtName != $type) {
            if (!($menu = $menu->parent)) return null;
            $mtName = $menu->mType->name;
        }
        
        return $menu->id;
        
    }
    
    /**
     * 获取后台Menu的HTML字符串
     *
     * @param $rootId
     * @return string
     */
    function htmlTree($rootId) {
        
        $subs = $this->subs($rootId, $this->subIds($rootId));
        $html = $this->html($subs, $rootId);
        
        return $html; // substr($menu, 0, -10);
    }
    
    /**
     * 获取指定学校的菜单树
     *
     * @param integer $id 指定学校的菜单ID
     * @return JsonResponse
     */
    function schoolTree($id) {
        
        $rootMenu = $this->find($id);
        $nodes[] = [
            'id'     => $rootMenu['id'],
            'parent' => '#',
            'text'   => Html::tag('i', '', [
                    'class' => 'fa fa-university',
                ])->toHtml() . '&nbsp;&nbsp;' . $rootMenu['name'],
            'type'   => 'school',
        ];
        
        return response()->json(
            $this->nodes($id, $nodes)
        );
        
    }
    
    /**
     * 获取指定菜单对应的部门图标及名称
     *
     * @param $id
     * @return array
     */
    function department($id) {
        
        $icon = 'fa fa-send-o text-blue';
        $name = '运营';
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
     * 获取所有叶节点菜单
     *
     * @param null $rootMenuId
     * @return Collection|static[]
     */
    function leaves($rootMenuId = null) {
        
        $leafPath = [];
        $ids = $rootMenuId
            ? $this->subIds($rootMenuId)
            : $this->all()->pluck('id')->toArray();
        foreach ($ids as $id) {
            if (empty($this->find($id)->children->count())) {
                $path = $this->leafPath($id, $leafPath);
                $leaves[$id] = $path;
                $leafPath = [];
            }
        }
        
        return $leaves ?? [];
        
    }
    
    /**
     * 返回composer所需的view数据
     *
     * @return array
     */
    function compose() {
        
        $action = explode('/', Request::path())[1];
        if ($action == 'sort') {
            $menuId = Request::route('id');
            $tabIds = MenuTab::whereMenuId($menuId)->get()->sortBy('tab_order')->pluck('tab_id');
            $data = [
                'tabs'   => Tab::whereIn('id', $tabIds)
                    ->orderByRaw(DB::raw("FIELD(id, {$tabIds->join(',')})"))
                    ->get(),
                'menuId' => $menuId,
            ];
        } else {
            $role = Auth::user()->role();
            $gIds = Group::whereIn('name', Constant::SUPER_ROLES)
                ->pluck('id', 'name');
            $tabs = Tab::whereEnabled(1)->get();
            if ($role == '企业') {
                $tabs = $tabs->where('group_id', '<>', $gIds['运营']);
            } elseif ($role == '学校') {
                $tabs = $tabs->whereIn('group_id', [0, $gIds['学校']]);
            }
            $menu = Menu::find(Request::route('id'));
            $menuTabs = $menu ? $menu->tabs : null;
            $data = [
                'tabs'         => $tabs->pluck('comment', 'id'),
                'icons'        => Icon::pluck('name', 'id'),
                'selectedTabs' => $menuTabs ? $menuTabs->pluck('id') : null,
            ];
        }
        
        return $data;
        
    }
    
    /**
     * 获取用于显示jstree的菜单数据
     *
     * @param null $id
     * @return JsonResponse
     */
    private function jsTree($id) {
        
        $root = $this->find($id);
        $subs = $this->subs(
            $id, $this->subIds($id), true
        );
        $subs[$id] = [
            'parent_id'    => null,
            'name'         => $root->name,
            'icon'         => $root->icon ? $root->icon->name : 'fa fa-circle-o',
            'menu_type_id' => $root->menu_type_id,
            'enabled'      => 1,
        ];
        foreach ($subs as $key => $sub) {
            $name = $sub['name'];
            if (isset($sub['parent_id'])) {
                $icon = $sub['icon'];
                $iconHtml = Html::tag('i', '', [
                    'class' => !$icon ? 'fa fa-circle-o' : $icon,
                    'style' => 'width: 20px;',
                ])->toHtml();
                $name = $iconHtml . '&nbsp;&nbsp;' . $name;
            }
            $mt = MenuType::find($sub['menu_type_id']);
            $type = $mt->remark;
            $text = Html::tag('span', $name, [
                'class' => $sub['enabled'] ? $mt->color : 'text-gray',
            ])->toHtml();
            if ($type == '企业') {
                $corpId = Corp::whereMenuId($key)->first()->id;
            } elseif ($type == '学校') {
                $corpId = School::whereMenuId($key)->first()->corp_id;
            }
            $tree[] = [
                'id'      => $key,
                'parent'  => $sub['parent_id'] ?? '#',
                'text'    => $text,
                'type'    => $type,
                'corp_id' => $corpId ?? null,
            ];
        }
        
        return response()->json($tree ?? []);
        
    }
    
    /**
     * 获取指定菜单下的所有菜单对象
     *
     * @param $rootId
     * @param $childrenIds
     * @param bool $disabled 是否获取未启用的菜单对象
     * @return Collection|static[]
     */
    private function subs($rootId, $childrenIds = null, $disabled = false) {
        
        $user = Auth::user();
        if ($rootId == 1) {
            $menus = $this->where('id', '<>', 1)
                ->orderBy('position')->get()->toArray();
        } else {
            if (!in_array($user->role(), Constant::SUPER_ROLES)) {
                $menus = GroupMenu::with('menu')
                    ->where('group_id', $user->group_id)
                    ->get()->pluck('menu')->toArray();
                $arr = [];
                foreach ($menus as $key => $menu) {
                    $arr[$key] = $menu['position'];
                }
                array_multisort($arr, SORT_ASC, $menus);
            } else {
                $menus = $this->whereIn('id', $childrenIds)
                    ->orderBy('position')
                    ->get()->toArray();
            }
        }
        foreach ($menus as $menu) {
            if (!$disabled && !$menu['enabled']) continue;
            $icon = Icon::find($menu['icon_id'] ?? null);
            $subs[$menu['id']] = [
                'parent_id'    => $menu['parent_id'],
                'name'         => $menu['name'],
                'uri'          => $menu['uri'],
                'icon'         => $icon ? $icon->name : 'fa fa-circle-o',
                'menu_type_id' => $menu['menu_type_id'],
                'enabled'      => $menu['enabled'],
            ];
        }
        
        return $subs ?? [];
        
    }
    
    /**
     * 指定菜单可否移动至指定的菜单下
     *
     * @param $id
     * @param $parentId
     * @return bool
     * @throws ReflectionException
     */
    private function movable($id, $parentId) {
        
        if (!isset($id, $parentId)) return false;
        $user = Auth::user();
        if ($user->role() != '运营') {
            $menuIds = $this->menuIds();
            abort_if(
                !in_array($id, $menuIds) || !in_array($parentId, $menuIds),
                HttpStatusCode::UNAUTHORIZED,
                __('messages.forbidden')
            );
        }
        $parentType = $this->find($parentId)->mType->name;
        switch ($this->find($id)->mType->name) {
            case '运营':
                return $parentType == '根';
            case '企业':
                return $parentType == '运营';
            case '学校':
                # 如果学校所属企业发生变化，则不允许移动
                $school = School::whereMenuId($id)->first();
                
                return $parentType !== '企业' ? false
                    : $school->corp_id == Corp::whereMenuId($parentId)->first()->id;
            case '其他':
                return true;
            default:
                return false;
        }
        
    }
    
    /**
     * 更改菜单所处位置
     *
     * @param $id
     * @param $parentId
     * @return bool
     * @throws Throwable
     */
    private function move($id, $parentId) {
        
        try {
            DB::transaction(function () use ($id, $parentId) {
                $menu = $this->find($id);
                $menu->parent_id = $parentId === '#' ? null : intval($parentId);
                $menu->save();
                /** 当企业类菜单所属运营类菜单发生变化时，更新企业所属运营者及所属部门 */
                if ($menu->mType->name == '企业') {
                    $corp = Corp::whereMenuId($menu->id)->first();
                    $company = Company::whereMenuId($menu->parent_id)->first();
                    Department::find($corp->department_id)->update(['parent_id' => $company->department_id]);
                    $corp->update(['company_id' => $company->id]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
    private function html($menus, $currentParent, $currLevel = 0, $prevLevel = -1) {
        
        static $html;
        $activeId = session('menuId');
        foreach ($menus as $menuId => $menu) {
            $mId = $menuId;
            $mName = $menu['name'];
            $mIcon = $menu['icon'];
            $mUri = $menu['uri'];
            // $hasChildren = $this->find($mId)->children->count();
            $hasChildren = $this->whereParentId($mId)->where('enabled', 1)->count();
            $mUrl = empty($mUri) ? 'pages/' . $mId : $mUri;
            if ($currentParent == $menu['parent_id']) {
                if ($hasChildren) {
                    $html .= sprintf(
                        self::TREE,
                        $mId == $activeId ? ' active' : '', $mIcon, $mName
                    );
                } else {
                    $html .= sprintf(
                        self::SIMPLE,
                        $mId == $activeId ? ' class="active"' : '', $mId, $mUrl, $mIcon, $mName
                    );
                }
                if ($currLevel > $prevLevel) {
                    $prevLevel = $currLevel;
                }
                if ($hasChildren) {
                    $currLevel++;
                    $this->html($menus, $menuId, $currLevel, $prevLevel);
                    $currLevel--;
                }
                
            }
        }
        if ($currLevel == $prevLevel) $html .= "</ul></li>";
        
        return $html;
        
    }
    
    /**
     * 获取指定学校的所有子菜单
     *
     * @param integer $id
     * @param array $nodes
     * @return array
     */
    private function nodes($id, &$nodes = []) {
        
        $children = $this->find($id)->children;
        foreach ($children as $child) {
            $name = $child['name'];
            if (isset($child['parent_id'])) {
                $icon = $this->find($child['id'])->icon;
                $iconHtml = Html::tag('i', '', [
                    'class' => $icon ? $icon->name : 'fa fa-circle-o',
                ])->toHtml();
                $name = $iconHtml . '&nbsp;&nbsp;' . $name;
            }
            $nodes[] = [
                'id'     => $child->id,
                'parent' => $child->parent_id,
                'text'   => $name,
                'type'   => 'other',
            ];
            $this->nodes($child->id, $nodes);
        }
        
        return $nodes;
        
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
        if (!$menu) return '';
        $path[] = $menu->name;
        if (isset($menu->parent_id)) {
            $this->leafPath($menu->parent_id, $path);
        }
        krsort($path);
        
        return join(' . ', $path);
        
    }
    
}

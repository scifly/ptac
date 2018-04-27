<?php

namespace App\Models;

use App\Events\DepartmentCreated;
use App\Events\DepartmentMoved;
use App\Events\DepartmentUpdated;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
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

/**
 * App\Models\Department 部门
 *
 * @property int $id
 * @property int|null $parent_id 父部门ID
 * @property string $name 部门名称
 * @property string|null $remark 部门备注
 * @property int|null $order 在父部门中的次序值。order值大的排序靠前
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property int $department_type_id 所属部门类型ID
 * @property-read Collection|Department[] $children
 * @property-read Company $company
 * @property-read Corp $corp
 * @property-read DepartmentType $departmentType
 * @property-read Grade $grade
 * @property-read Department|null $parent
 * @property-read School $school
 * @property-read Squad $squad
 * @property-read Collection|\App\Models\User[] $users
 * @method static Builder|Department whereCreatedAt($value)
 * @method static Builder|Department whereDepartmentTypeId($value)
 * @method static Builder|Department whereEnabled($value)
 * @method static Builder|Department whereId($value)
 * @method static Builder|Department whereName($value)
 * @method static Builder|Department whereOrder($value)
 * @method static Builder|Department whereParentId($value)
 * @method static Builder|Department whereRemark($value)
 * @method static Builder|Department whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Department extends Model {

    // todo: needs to be optimized

    use ModelTrait;

    protected $fillable = [
        'parent_id', 'department_type_id', 'name',
        'remark', 'order', 'enabled',
    ];

    protected $menu;
    
    function __construct(array $attributes = []) {
        
        parent::__construct($attributes);
        $this->menu = app()->make('App\Models\Menu');
        
    }
    
    /**
     * 返回所属的部门类型对象
     *
     * @return BelongsTo
     */
    function departmentType() { return $this->belongsTo('App\Models\DepartmentType'); }

    /**
     * 返回对应的运营者对象
     *
     * @return HasOne
     */
    function company() { return $this->hasOne('App\Models\Company'); }

    /**
     * 返回对应的班级对象
     *
     * @return HasOne
     */
    function corp() { return $this->hasOne('App\Models\Corp'); }

    /**
     * 返回对应的学校对象
     *
     * @return HasOne
     */
    function school() { return $this->hasOne('App\Models\School'); }

    /**
     * 返回对应的年级对象
     *
     * @return HasOne
     */
    function grade() { return $this->hasOne('App\Models\Grade'); }

    /**
     * 返回对应的班级对象
     *
     * @return HasOne
     */
    function squad() { return $this->hasOne('App\Models\Squad'); }

    /**
     * 获取指定部门包含的所有用户对象
     *
     * @return BelongsToMany
     */
    function users() { return $this->belongsToMany('App\Models\User', 'departments_users'); }

    /**
     * 返回上级部门对象
     *
     * @return BelongsTo
     */
    function parent() {

        return $this->belongsTo('App\Models\Department', 'parent_id');

    }

    /**
     * 获取指定部门的子部门
     *
     * @return HasMany
     */
    function children() {

        return $this->hasMany('App\Models\Department', 'parent_id', 'id');

    }
    
    /**
     * 返回所有叶节点部门
     *
     * @return array
     */
    function leaves() {
        
        $leaves = [];
        $leafPath = [];
        $departments = self::nodes();
        /** @var Department $department */
        foreach ($departments as $department) {
            if (empty($department->children()->count())) {
                $path = self::leafPath($department->id, $leafPath);
                $leaves[$department->id] = $path;
                $leafPath = [];
            }
        }
        
        return $leaves;

    }

    /**
     * 根据根部门ID返回所有下级部门对象
     *
     * @param null $rootId
     * @return Collection|static[]
     */
    function nodes($rootId = null) {

        $nodes = new Collection();
        if (!isset($rootId)) {
            $nodes = self::all();
        } else {
            $root = self::find($rootId);
            $nodes->push($root);
            self::getChildren($rootId, $nodes);
        }

        return $nodes;

    }

    /**
     * 获取指定部门的完整路径
     *
     * @param $id
     * @param array $path
     * @return string
     */
    function leafPath($id, array &$path) {

        $department = self::find($id);
        if (!isset($department)) {
            return '';
        }
        $path[] = $department->name;
        if (isset($department->parent_id)) {
            self::leafPath($department->parent_id, $path);
        }
        krsort($path);

        return implode(' . ', $path);

    }
    
    /**
     * 返回Department列表
     *
     * @return array
     */
    function departments() {

        $departments = self::nodes();
        $departmentList = [];
        foreach ($departments as $department) {
            $departmentList[$department->id] = $department->name;
        }

        return $departmentList;

    }

    /**
     * 创建部门
     *
     * @param array $data
     * @param bool $fireEvent
     * @return $this|bool|Model
     */
    function store(array $data, $fireEvent = false) {

        $department = self::create($data);
        if ($department && $fireEvent) {
            event(new DepartmentCreated($department));
            return $department;
        }

        return $department ? $department : false;

    }

    /**
     * 更新部门
     *
     * @param array $data
     * @param $id
     * @param bool $fireEvent
     * @return bool|Collection|Model|null|static|static[]
     */
    function modify(array $data, $id, $fireEvent = false) {

        $department = self::find($id);
        $updated = $department->update($data);
        if ($updated && $fireEvent) {
            event(new DepartmentUpdated($department));
            return $department;
        }

        return $updated ? $department : false;

    }
    
    /**
     * 删除部门
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     * @throws \Throwable
     */
    function remove($id) {

        $department = self::find($id);
        if (!$department) { return false; }
        if (!self::removable($department)) { return false; }
        try {
            DB::transaction(function () use ($id, $department) {
                # 删除指定的Department记录
                $department->delete();
                # 移除指定部门与用户的绑定记录
                $departmentUser = new DepartmentUser();
                $departmentUser::whereDepartmentId($id)->delete();
                # 删除指定部门的所有子部门记录, 以及与用户的绑定记录
                $subDepartments = self::whereParentId($id)->get();
                foreach ($subDepartments as $department) {
                    self::remove($department->id);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }

    /**
     * 更改部门所处位置
     *
     * @param $id
     * @param $parentId
     * @param bool $fireEvent
     * @return bool
     */
    function move($id, $parentId, $fireEvent = false) {

        $deparment = self::find($id);
        if (!isset($deparment)) { return false; }
        $deparment->parent_id = $parentId === '#' ? null : intval($parentId);
        $moved = $deparment->save();
        if ($moved && $fireEvent) {
            event(new DepartmentMoved(self::find($id)));
            return true;
        }

        return $moved ? true : false;

    }
    
    /**
     * 部门列表
     *
     * @param null $id
     * @param null $parentId
     * @return bool|JsonResponse
     */
    function index($id = null, $parentId = null) {

        if (Request::has('id')) {
            # 部门列表
            return response()->json(
                $this->tree(
                    $this->rootDepartmentId(true)
                )
            );
        } else if (Request::has('data')) {
            # 保存部门排序
            $orders = Request::get('data');
            foreach ($orders as $id => $order) {
                $department = $this->find($id);
                if (isset($department)) {
                    $department->order = $order;
                    $department->save();
                }
            }
        } else {
            # 移动部门
            $department = $this->find($id);
            $parentDepartment = $this->find($parentId);
            abort_if(
                !$department || !$parentDepartment,
                HttpStatusCode::NOT_FOUND
            );
            if ($department->movable($id, $parentId)) {
                $department->move($id, $parentId, true);
            }
        }
        
        return '';
        
    }
    
    /**
     * 获取用于显示jstree的部门数据
     *
     * @param null $rootId
     * @return array
     */
    function tree($rootId = null) {

        $departments = $this->nodes();
        if (isset($rootId)) {
            $departments = $this->nodes($rootId);
        } else {
            $user = Auth::user();
            if ($user->group->name != '运营') {
                # todo: this is the trickest one if a user belongs to departments that
                # todo: have no direct relationships
                $departments = $this->nodes($user->topDeptId());
            }
        }
        $nodes = [];
        for ($i = 0; $i < sizeof($departments); $i++) {
            $parentId = $i == 0 ? '#' : $departments[$i]['parent_id'];
            $departmentType = DepartmentType::find($departments[$i]['department_type_id'])->name;
            $name = $departments[$i]['name'];
            $enabled = $departments[$i]['enabled'];
            $color = Constant::NODE_TYPES[$departmentType]['color'];
            $nodes[] = [
                'id' => $departments[$i]['id'],
                'parent' => $parentId,
                'text' => sprintf(Snippet::NODE_TEXT, $enabled ? $color : 'text-gray', $name),
                'type' => Constant::DEPARTMENT_TYPES[$departmentType],
            ];
        }

        return $nodes;

    }
    
    /**
     * 获取当前用户的根部门ID
     *
     * @param bool $subRoot
     * @return int|mixed
     */
    function rootDepartmentId($subRoot = false) {
        
        $user = Auth::user();
        $role = $user->group->name;
        $rootDepartmentTypeId = DepartmentType::whereName('根')->first()->id;
        $rootDId = Department::whereDepartmentTypeId($rootDepartmentTypeId)->first()->id;
        # 当前菜单id
        $menuId = session('menuId');
        # 学校的根菜单id
        $smId = $this->menu->menuId($menuId);
        # 学校的根部门id
        $sdId = $smId ? School::whereMenuId($smId)->first()->department_id : null;
        # 企业的根菜单id
        $cmId = $this->menu->menuId($menuId, '企业');
        # 企业的根部门id
        $cdId = $cmId ? Corp::whereMenuId($cmId)->first()->department_id : null;
        switch ($role) {
            case '运营':
                return !$subRoot ? $rootDId : ($sdId ?? ($cdId ?? $rootDId));
            case '企业':
                return !$subRoot ? $cdId : ($sdId ?? $cdId);
            case '学校':
                return $sdId;
            default:
                return School::find($user->educator->school_id)->department_id;
        }
        
    }
    
    /**
     * 选中的部门节点
     *
     * @param $ids
     * @return array
     */
    function selectedNodes($ids) {

        $departments = self::whereIn('id', $ids)->get()->toArray();
        $data = [];
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) ? $department['parent_id'] : '#';
            $text = $department['name'];
            $departmentType = DepartmentType::find($department['department_type_id'])->name;
            switch ($departmentType) {
                case '根': $type = 'root'; $icon = 'fa fa-sitemap'; break;
                case '运营': $type = 'company'; $icon = 'fa fa-building'; break;
                case '企业': $type = 'corp'; $icon = 'fa fa-weixin'; break;
                case '学校': $type = 'school'; $icon = 'fa fa-university'; break;
                case '年级': $type = 'grade'; $icon = 'fa fa-object-group'; break;
                case '班级': $type = 'class'; $icon = 'fa fa-users'; break;
                default: $type = 'other'; $icon = 'fa fa-list'; break;
            }
            $data[] = [
                'id' => $department['id'],
                'parent' => $parentId,
                'text' => $text,
                'icon' => $icon,
                'type' => $type,
            ];
        }

        return $data;

    }

    /**
     * 判断指定的节点能否移至指定的节点下
     *
     * @param $id
     * @param $parentId
     * @return bool
     */
    function movable($id, $parentId) {

        if (!isset($id, $parentId)) { return false; }
        $allowedDepartmentIds = $this->departmentIds(Auth::id());
        # 如果部门(被移动的部门和目标部门）不在当前用户的可见范围内，则抛出401异常
        abort_if(
            !in_array($id, $allowedDepartmentIds) || !in_array($parentId, $allowedDepartmentIds),
            HttpStatusCode::UNAUTHORIZED,
            __('messages.forbidden')
        );
        $type = self::find($id)->departmentType->name;
        $parentType = self::find($parentId)->departmentType->name;
        switch ($type) {
            case '运营': return $parentType == '根';
            case '企业': return $parentType == '运营';
            case '学校': return $parentType == '企业';
            case '年级': return $parentType == '学校' or $parentType == '其他';
            case '班级': return $parentType == '年级' or $parentType == '其他';
            case '其他': return !($parentType == '企业' or $parentType == '运营');
            default: return false;
        }

    }

    /**
     * 根据年级的部门ID获取所属学校的ID
     *
     * @param $id
     * @return int|mixed
     */
    function getSchoolId($id) {

        $parent = self::find($id)->parent;
        if ($parent->departmentType->name == '学校') {
            $departmentId = $parent->id;
            return School::whereDepartmentId($departmentId)->first()->id;
        } else {
            return self::getSchoolId($parent->id);
        }

    }

    /**
     * 根据班级的部门ID获取所属年级的ID
     *
     * @param $id
     * @return int|mixed
     */
    function getGradeId($id) {

        $parent = self::find($id)->parent;
        if ($parent->departmentType->name == '年级') {
            $departmentId = $parent->id;
            return Grade::whereDepartmentId($departmentId)->first()->id;
        } else {
            return self::getGradeId($parent->id);
        }

    }
    
    /**
     * 返回指定部门所处的级别
     *
     * @param integer $id 部门ID
     * @param integer $level 部门所处级别
     * @return int|null
     */
    function level($id, &$level) {
        
        $department = self::find($id);
        if (!$department) { return null; }
        $parent = $department->parent;
        if ($parent) {
            $level += 1;
            self::level($parent->id, $level);
        }
        
        return $level;
        
    }
    
    /**
     * 获取联系人树
     *
     * @return array|\Illuminate\Http\JsonResponse
     */
    function contacts() {
        
        $user = Auth::user();
        $contacts = [];
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            $departmentId = School::find($this->schoolId())->department_id;
            $visibleNodes = $this->tree($departmentId);
            for ($i = 0; $i < sizeof($visibleNodes); $i++) {
                $nodes[$i]['selectable'] = 1;
            }
            foreach ($visibleNodes as $node) {
                # 读取当前部门下的所有用户
                $users = $this->find($node['id'])->users;
                foreach ($users as $u) {
                    $contacts[] = [
                        'id' => 'user-' . $node['id'] . '-' . $u->id,
                        'parent' => $node['id'],
                        'text' => $u->realname,
                        'selectable' => 1,
                        'type' => 'user',
                    ];
                }
            }
        } else {
            $nodes = $this->tree($this->topDeptId());
            # 对当前用户可见的所有部门id
            $allowedDepartmentIds = $this->departmentIds($user->id);
            # 对当前用户可见部门的所有上级部门id
            $allowedParentIds = [];
            foreach ($allowedDepartmentIds as $id) {
                $allowedParentIds[$id] = $this->parentIds($id);
            }
            for ($i = 0; $i < sizeof($nodes); $i++) {
                $departmentId = $nodes[$i]['id'];
                $nodes[$i]['selectable'] = in_array($departmentId, $allowedDepartmentIds) ? 1 : 0;
            }
            # 对当前用户可见的所有联系人树节点
            $visibleNodes = [];
            foreach ($nodes as $node) {
                if (!$node['selectable']) {
                    foreach ($allowedParentIds as $departmentId => $parentIds) {
                        if (in_array($node['id'], $parentIds)) {
                            $visibleNodes[] = $node;
                            break;
                        }
                    }
                }
            }
            $contacts = [];
            foreach ($visibleNodes as $node) {
                if ($node['selectable']) {
                    # 读取当前部门下的所有用户
                    $users = $this->find($node['id'])->users;
                    foreach ($users as $u) {
                        $contacts[] = [
                            'id' => 'user-' . $node['id'] . '-' . $u->id,
                            'parent' => $node['id'],
                            'text' => $u->realname,
                            'selectable' => 1,
                            'type' => 'user',
                        ];
                    }
                }
            }
        }
        
        return response()->json(
            array_merge($visibleNodes, $contacts)
        );
    
    }

    /**
     * 获取一个数组部门下 连同子部门下的所有用户
     *
     * @param $toparty
     * @return array
     */
    function getPartyUser ($toparty) {

        $users = [];
        $depts = new Collection();
        foreach ($toparty as $p) {
            self::getChildrenNode($p, $depts);
        }
        $items = $depts->toArray();
        if (empty($items)) { return $users; }
        foreach ($items as $i) {
            $deptUsers = DepartmentUser::whereDepartmentId($i['id'])->get();
            if ($deptUsers) {
                foreach ($deptUsers as $d) {
                    $user = User::find($d->user_id);
                    if ($user) { $users[] = $user; }
                }
            }
        }

        return $users;

    }

    /**
     * 返回指定部门所有子部门的id
     *
     * @param $id
     * @return array
     */
    function subDepartmentIds($id) {

        static $childrenIds;
        $firstIds = Department::whereParentId($id)->get(['id'])->toArray();
        if ($firstIds) {
            foreach ($firstIds as $firstId) {
                $childrenIds[] = $firstId['id'];
                self::subDepartmentIds($firstId['id']);
            }
        }

        return $childrenIds ?? [];

    }
    
    /**
     * 返回指定部门所属学校的部门id
     *
     * @param $id
     * @return int|mixed
     */
    function schoolDeptId($id) {
        
        $department = $this->find($id);
        if ($department->department_type->name != '学校') {
            return self::schoolDeptId($department->parent_id);
        }
        
        return $department->id;
        
    }

    /**
     * 返回用户所处的顶级部门id
     *
     * @return int
     */
    private function topDeptId() {
        
        $ids = Auth::user()->departments->pluck('id')->toArray();
        $levels = [];
        foreach ($ids as $id) {
            $level = 0;
            $levels[$id] = self::level($id, $level);
        }
        asort($levels);
        reset($levels);
        $topLevelId = key($levels);
        
        return self::find($topLevelId)->parent->id;
        
    }

    /**
     * 根据Department ID返回所有下级部门
     *
     * @param $id
     * @param Collection $nodes
     */
    private function getChildren($id, Collection &$nodes) {

        $node = self::find($id);
        foreach ($node->children as $child) {
            $nodes->push($child);
            self::getChildren($child->id, $nodes);
        }

    }

    /**
     * 根据Department ID返回所有下级部门 含本身
     *
     * @param $id
     * @param Collection $nodes
     */
    private function getChildrenNode($id, Collection &$nodes) {

        $node = self::find($id);
        $nodes->push($node);
        foreach ($node->children as $child) {
            $nodes->push($child);
            self::getChildren($child->id, $nodes);
        }

    }
    
    /**
     * 返回指定部门的所有上级（校级及以下）部门id
     *
     * @param $id
     * @return array
     */
    private function parentIds($id) {
    
        static $ids = [];
    
        $d = $this->find($id);
        $p = $d->parent;
        while ($p->departmentType->name != '学校') {
            $ids[] = $p->id;
            return $this->parentIds($p->id);
        }
        $ids[] = $p->id;
    
        return $ids;
    
    }

}

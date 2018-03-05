<?php

namespace App\Models;

use App\Events\DepartmentCreated;
use App\Events\DepartmentMoved;
use App\Events\DepartmentUpdated;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
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
                $departments = $this->nodes($user->topDeptId());
            }
        }
        $nodes = [];
        for ($i = 0; $i < sizeof($departments); $i++) {
            $parentId = $i == 0 ? '#' : $departments[$i]['parent_id'];
            $text = $departments[$i]['name'];
            $departmentType = DepartmentType::find($departments[$i]['department_type_id'])->name;
            switch ($departmentType) {
                case '根': $type = 'root'; break;
                case '运营': $type = 'company'; break;
                case '企业': $type = 'corp'; break;
                case '学校': $type = 'school'; break;
                case '年级': $type = 'grade'; break;
                case '班级': $type = 'class'; break;
                default: $type = 'other'; break;
            }
            $nodes[] = [
                'id' => $departments[$i]['id'],
                'parent' => $parentId,
                'text' => $text,
                'type' => $type,
            ];
        }

        return $nodes;

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

    function showDepartments($ids) {

        $departments = self::whereIn('id', $ids)->get()->toArray();
        $parentDepartmentIds = [];
        $departmentUsers = [];
        foreach ($departments as $department) {
            $parentDepartmentIds[] = $department->id;
        }
        foreach ($parentDepartmentIds as $id) {
            $department = $this->find($id);
            foreach ($department->users as $user) {
                $departmentUsers[] = [
                    'id' => $id . 'UserId_' . $user['id'],
                    'parent' => $id,
                    'text' => $user['username'],
                    'icon' => 'fa fa-user',
                    'type' => 'user',
                ];
            }
        }
        $nodes = [];
        foreach ($departmentUsers as $departmentUser) {
            $nodes[] = $departmentUser;
        }
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) && in_array($department['parent_id'], $parentDepartmentIds)
                ? $department['parent_id'] : '#';
            $text = $department['name'];
            $departmentType = DepartmentType::find($department['department_type_id'])->name;
            switch ($departmentType) {
                case '根': $type = 'root'; $icon = 'fa fa-sitemap'; break;
                case '运营': $type = 'company'; $icon = 'fa fa-building'; break;
                case '企业': $type = 'corp'; $icon = 'fa fa-weixin'; break;
                case '学校': $type = 'school'; $icon = 'fan fa-university'; break;
                case '年级': $type = 'grade'; $icon = 'fa fa-object-group'; break;
                case '班级': $type = 'class'; $icon = 'fa fa-users'; break;
                default: $type = 'other'; $icon = 'fa fa-list'; break;
            }
            $nodes[] = [
                'id' => $department['id'],
                'parent' => $parentId,
                'text' => $text,
                'icon' => $icon,
                'type' => $type,
            ];
        }

        return $nodes;

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
     * 根据登录用户展示部门列表
     *
     * @param $ids
     * @return array
     */
    function getDepartment($ids) {

        $departments = self::whereIn('id', $ids)->get()->toArray();
        $departmentParentIds = [];

        foreach ($departments as $key => $department) {
            $departmentParentIds[] = $department['id'];
        }
        $data = [];
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) && in_array($department['parent_id'], $departmentParentIds)
                ? $department['parent_id'] : '#';
            $text = $department['name'];
            $departmentType = DepartmentType::find($department['department_type_id'])->name;
            switch ($departmentType) {
                case '根':  $type = 'root'; $icon = 'fa fa-sitemap'; break;
                case '运营': $type = 'company'; $icon = 'fa fa-building'; break;
                case '企业': $type = 'corp'; $icon = 'fa fa-weixin'; break;
                case '学校': $type = 'school'; $icon = 'fa fa-university'; break;
                case '年级': $type = 'grade'; $icon = 'fa fa-users'; break;
                case '班级': $type = 'class'; $icon = 'fa fa-user'; break;
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
     * 返回指定用户所处顶级部门的类型
     *
     * @param $userId
     * @return null|string
     */
    function groupLevel($userId) {

        $user = User::find($userId);
        if (!$user) { return null; }
        $group = $user->group;
        if (isset($group->school_id)) { return 'school'; }
        $topDepartmentId = $user->topDeptId();
        $departmentType = self::find($topDepartmentId)->departmentType->name;
        switch ($departmentType) {
            case '根': return 'root';
            case '运营': return 'company';
            case '企业': return 'corp';
            default: return null;
        }

    }

    /**
     * 获取用户所属学校/年级/班级的学校/年级/班级ID及名称
     *
     * @param $topDepartmentId
     * @return array|null
     */
    function topDepartment($topDepartmentId) {

        $type = self::find($topDepartmentId)->departmentType->name;
        if (in_array($type, ['运营', '企业'])) {
            return null;
        }
        while (!in_array($type, ['学校', '年级', '班级'])) {
            $parent = self::find($topDepartmentId)->parent;
            $type = $parent->departmentType->name;
            $topDepartmentId = $parent->id;
        }
        $department = null;
        switch ($type) {
            case '学校':
                $department = School::whereDepartmentId($topDepartmentId)
                    ->where('enabled', 1)
                    ->pluck('name', 'id')
                    ->toArray();
                break;
            case '年级':
                $department = Grade::whereDepartmentId($topDepartmentId)
                    ->where('enabled', 1)
                    ->pluck('name', 'id')
                    ->toArray();
                break;
            case '班级':
                $department = Squad::whereDepartmentId($topDepartmentId)
                    ->where('enabled', 1)
                    ->pluck('name', 'id')
                    ->toArray();
                break;
            default:
                break;
        }
        
        return $department ? ['type' => $type, 'department' => $department] : null;

    }
    
    function contacts() {
        
        $user = Auth::user();
        $role = $user->group->name;
        $departmentId = School::find($this->schoolId())->department_id;
        $contacts = [];
        if (in_array($role, Constant::SUPER_ROLES)) {
            $tree = self::tree($departmentId);
            foreach ($tree as &$t) {
                $t['seletable'] =1;
                $t['role'] ='dept';
                $t['type'] = $t['id'] == 0 ? '#' : 'dept';
                # 读取当前部门下的所有用户
                $users = self::find($t['id'])->users;
                /** @var User $u */
                foreach ($users as $u) {
                    $contacts[] = [
                        'id' => 'user-' . $u->id,
                        'parent' => $t['id'],
                        'text' => $u->realname,
                        'seletable' => 1,
                        'type' => 'user',
                        'role' => 'user',
                    ];
                }
            }
            return  array_merge($tree, $contacts);
        } else {
            $departmentId = self::topDeptId();
            $nodes = self::nodes($departmentId);
            $data = [];
            $belongedDeptIds = $user->departments
                ->pluck('id')
                ->toArray();
            for ($i = 0; $i < sizeof($nodes); $i++) {
                $parentId = $i == 0 ? '#' : $nodes[$i]['parent_id'];
                $type = $i == 0 ? '#' : 'dept';
                $text = $nodes[$i]['name'];
                if (in_array($nodes[$i]['id'], $belongedDeptIds)) {
                    $seletable = 1;
                } else {
                    $seletable = 0;
                    foreach ($belongedDeptIds as $pId) {
                        if (self::find($nodes[$i]['id'])->parent_id == $pId) {
                            $seletable = 1;
                            break;
                        };
                    }
                }
                $data[] = [
                    'id' => $nodes[$i]['id'],
                    'parent' => $parentId,
                    'text' => $text,
                    'seletable' => $seletable,
                    'type' => $type, //0->部门；1->用户
                    'role' => 'dept', //0->部门；1->用户
                ];
            }
            $contacts = [];
            foreach ($data as $datum) {
                if ($datum['seletable']) {
                    # 读取当前部门下的所有用户
                    $users = self::find($datum['id'])->users;
                    /** @var User $u */
                    foreach ($users as $u) {
                        $contacts[] = [
                            'id' => 'user-' . $u->id,
                            'parent' => $datum['id'],
                            'text' => $u->realname,
                            'seletable' => 1,
                            'type' => 'user',
                            'role' => 'user',
                        ];
                    }
                }
            }
            
            return response()->json(
                array_merge($data, $contacts)
            );

        }
        
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

        return $childrenIds;

    }

    /**
     * 返回指定部门所属学校
     *
     * @param $dept
     * @return int|mixed
     */
    function schoolDeptId($dept) {
        
        $de = Department::whereId($dept)->first();
        if ($de->department_type_id != 4) {
            return self::schoolDeptId($de->parent_id);
        }
        return $de->id;
        
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

}

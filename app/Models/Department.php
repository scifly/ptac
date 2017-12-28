<?php

namespace App\Models;

use App\Events\DepartmentCreated;
use App\Events\DepartmentMoved;
use App\Events\DepartmentUpdated;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
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
 * @property int $corp_id 所属企业ID
 * @property int $school_id 所属学校ID
 * @property string $name 部门名称
 * @property string|null $remark 部门备注
 * @property int|null $order 在父部门中的次序值。order值大的排序靠前
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Department whereCorpId($value)
 * @method static Builder|Department whereCreatedAt($value)
 * @method static Builder|Department whereEnabled($value)
 * @method static Builder|Department whereId($value)
 * @method static Builder|Department whereName($value)
 * @method static Builder|Department whereOrder($value)
 * @method static Builder|Department whereParentId($value)
 * @method static Builder|Department whereRemark($value)
 * @method static Builder|Department whereSchoolId($value)
 * @method static Builder|Department whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Corp $corp
 * @property-read School $school
 * @property-read Collection|Menu[] $children
 * @property-read Department|null $parent
 * @property-read Collection|Department[] $users
 * @property int $department_type_id 所属部门类型ID
 * @property-read Company $company
 * @property-read Grade $grade
 * @property-read Squad $squad
 * @method static Builder|Department whereDepartmentTypeId($value)
 * @property-read DepartmentType $departmentType
 */
class Department extends Model {

    use ModelTrait;

    protected $fillable = [
        'parent_id', 'department_type_id', 'name',
        'remark', 'order', 'enabled',
    ];

    protected $roles = [
        '运营', '企业', '学校'
    ];

    /**
     * 返回所属的部门类型对象
     *
     * @return BelongsTo
     */
    public function departmentType() { return $this->belongsTo('App\Models\DepartmentType'); }

    /**
     * 返回对应的运营者对象
     *
     * @return HasOne
     */
    public function company() { return $this->hasOne('App\Models\Company'); }

    /**
     * 返回对应的班级对象
     *
     * @return HasOne
     */
    public function corp() { return $this->hasOne('App\Models\Corp'); }

    /**
     * 返回对应的学校对象
     *
     * @return HasOne
     */
    public function school() { return $this->hasOne('App\Models\School'); }

    /**
     * 返回对应的年级对象
     *
     * @return HasOne
     */
    public function grade() { return $this->hasOne('App\Models\Grade'); }

    /**
     * 返回对应的班级对象
     *
     * @return HasOne
     */
    public function squad() { return $this->hasOne('App\Models\Squad'); }

    /**
     * 获取指定部门包含的所有用户对象
     *
     * @return BelongsToMany
     */
    public function users() { return $this->belongsToMany('App\Models\User', 'departments_users'); }

    /**
     * 返回上级部门对象
     *
     * @return BelongsTo
     */
    public function parent() {

        return $this->belongsTo('App\Models\Department', 'parent_id');

    }

    /**
     * 返回所有叶节点部门
     *
     * @return array
     */
    public function leaves() {

        $leaves = [];
        $leafPath = [];
        $departments = $this->nodes();
        /** @var Department $department */
        foreach ($departments as $department) {
            if (empty($department->children()->count())) {
                $path = $this->leafPath($department->id, $leafPath);
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
    private function nodes($rootId = null) {

        $nodes = new Collection();
        if (!isset($rootId)) {
            $nodes = $this->all();
        } else {
            $root = $this->find($rootId);
            $nodes->push($root);
            $this->getChildren($rootId, $nodes);
        }

        return $nodes;

    }

    /**
     * 根据Department ID返回所有下级部门
     *
     * @param $id
     * @param Collection $nodes
     */
    private function getChildren($id, Collection &$nodes) {

        $node = $this->find($id);
        foreach ($node->children as $child) {
            $nodes->push($child);
            $this->getChildren($child->id, $nodes);
        }

    }
    /**
     * 根据Department ID返回所有下级部门 含本身
     *
     * @param $id
     * @param Collection $nodes
     */
    private function getChildrenNode($id, Collection &$nodes) {

        $node = $this->find($id);
        $nodes->push($node);
        foreach ($node->children as $child) {
            $nodes->push($child);
            $this->getChildren($child->id, $nodes);
        }

    }

    /**
     * 获取指定部门的子部门
     *
     * @return HasMany
     */
    public function children() {

        return $this->hasMany('App\Models\Department', 'parent_id', 'id');

    }

    /**
     * 获取指定部门的完整路径
     *
     * @param $id
     * @param array $path
     * @return string
     */
    private function leafPath($id, array &$path) {

        $department = $this->find($id);
        if (!isset($department)) {
            return '';
        }
        $path[] = $department->name;
        if (isset($department->parent_id)) {
            $this->leafPath($department->parent_id, $path);
        }
        krsort($path);

        return implode(' . ', $path);

    }

    /**
     * 返回Department列表
     *
     * @return array
     */
    public function departments() {

        $departments = $this->nodes();
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
    public function store(array $data, $fireEvent = false) {

        $department = $this->create($data);
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
    public function modify(array $data, $id, $fireEvent = false) {

        $department = $this->find($id);
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
    public function remove($id) {

        $department = $this->find($id);
        if (!$department) {
            return false;
        }
        if (!$this->removable($department)) {
            return false;
        }
        try {
            DB::transaction(function () use ($id, $department) {
                # 删除指定的Department记录
                $department->delete();
                # 移除指定部门与用户的绑定记录
                $departmentUser = new DepartmentUser();
                $departmentUser::whereDepartmentId($id)->delete();
                # 删除指定部门的所有子部门记录, 以及与用户的绑定记录
                $subDepartments = $this->where('parent_id', $id)->get();
                foreach ($subDepartments as $department) {
                    $this->remove($department->id);
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
    public function move($id, $parentId, $fireEvent = false) {

        $deparment = $this->find($id);
        if (!isset($deparment)) {
            return false;
        }
        $deparment->parent_id = $parentId === '#' ? null : intval($parentId);
        $moved = $deparment->save();
        if ($moved && $fireEvent) {
            event(new DepartmentMoved($this->find($id)));
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
    public function tree($rootId = null) {

        $departments = $this->nodes();
        if (isset($rootId)) {
            $departments = $this->nodes($rootId);
        } else {
            $user = Auth::user();
            if ($user->group->name != '运营') {
                $departments = $this->nodes($user->topDeptId($user));
            }
        }
        $data = [];
        for ($i = 0; $i < sizeof($departments); $i++) {
            $parentId = $i == 0 ? '#' : $departments[$i]['parent_id'];
            $text = $departments[$i]['name'];
            $departmentType = DepartmentType::find($departments[$i]['department_type_id'])->name;
            switch ($departmentType) {
                case '根':
                    $type = 'root';
                    break;
                case '运营':
                    $type = 'company';
                    break;
                case '企业':
                    $type = 'corp';
                    break;
                case '学校':
                    $type = 'school';
                    break;
                case '年级':
                    $type = 'grade';
                    break;
                case '班级':
                    $type = 'class';
                    break;
                default:
                    $type = 'other';
                    break;
            }
            $data[] = [
                'id' => $departments[$i]['id'],
                'parent' => $parentId,
                'text' => $text,
                'type' => $type,
            ];
        }

        return ($data);

    }

    /**
     * 选中的部门节点
     *
     * @param $ids
     * @return array
     */
    public function selectedNodes($ids) {

        $departments = $this->whereIn('id', $ids)->get()->toArray();
        $data = [];
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) ? $department['parent_id'] : '#';
            $text = $department['name'];
            $departmentType = DepartmentType::whereId($department['department_type_id'])->first()->name;
            switch ($departmentType) {
                case '根':
                    $type = 'root';
                    $icon = 'fa fa-sitemap';
                    break;
                case '运营':
                    $type = 'company';
                    $icon = 'fa fa-building';
                    break;
                case '企业':
                    $type = 'corp';
                    $icon = 'fa fa-weixin';
                    break;
                case '学校':
                    $type = 'school';
                    $icon = 'fa fa-university';
                    break;
                case '年级':
                    $type = 'grade';
                    $icon = 'fa fa-object-group';
                    break;
                case '班级':
                    $type = 'class';
                    $icon = 'fa fa-users';
                    break;
                default:
                    $type = 'other';
                    $icon = 'fa fa-list';
                    break;
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

    public function showDepartments($ids) {

        $departments = $this->whereIn('id', $ids)->get()->toArray();
        $departmentParentIds = [];
        $departmentUsers = [];
        foreach ($departments as $key => $department) {
            $departmentParentIds[] = $department['id'];
        }
        foreach ($departmentParentIds as $departmentId) {
            $department = Department::find($departmentId);
            foreach ($department->users as $user) {
                $departmentUsers[] = [
                    'id' => $departmentId . 'UserId_' . $user['id'],
                    'parent' => $departmentId,
                    'text' => $user['username'],
                    'icon' => 'fa fa-user',
                    'type' => 'user',
                ];
            }
        }
        $data = [];
        foreach ($departmentUsers as $departmentUser) {
            $data[] = $departmentUser;
        }
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) && in_array($department['parent_id'], $departmentParentIds) ? $department['parent_id'] : '#';
            $text = $department['name'];
            $departmentType = DepartmentType::whereId($department['department_type_id'])->first()->name;
            switch ($departmentType) {
                case '根':
                    $type = 'root';
                    $icon = 'fa fa-sitemap';
                    break;
                case '运营':
                    $type = 'company';
                    $icon = 'fa fa-building';
                    break;
                case '企业':
                    $type = 'corp';
                    $icon = 'fa fa-weixin';
                    break;
                case '学校':
                    $type = 'school';
                    $icon = 'fa fa-university';
                    break;
                case '年级':
                    $type = 'grade';
                    $icon = 'fa fa-object-group';
                    break;
                case '班级':
                    $type = 'class';
                    $icon = 'fa fa-users';
                    break;
                default:
                    $type = 'other';
                    $icon = 'fa fa-list';
                    break;
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
    public function movable($id, $parentId) {

        if (!isset($parentId)) {
            return false;
        }
        $type = $this->find($id)->departmentType->name;
        $parentType = $this->find($parentId)->departmentType->name;
        switch ($type) {
            case '运营':
                return $parentType == '根';
            case '企业':
                return $parentType == '运营';
            case '学校':
                return $parentType == '企业';
            case '年级':
                return $parentType == '学校' or $parentType == '其他';
            case '班级':
                return $parentType == '年级' or $parentType == '其他';
            case '其他':
                return !($parentType == '企业' or $parentType == '运营');
            default:
                return false;
        }

    }

    /**
     * 根据登录用户展示部门列表
     *
     * @param $ids
     * @return array
     */
    public function getDepartment($ids) {

        $departments = $this->whereIn('id', $ids)->get()->toArray();
        $departmentParentIds = [];

        foreach ($departments as $key => $department) {
            $departmentParentIds[] = $department['id'];
        }
        $data = [];
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) && in_array($department['parent_id'], $departmentParentIds) ? $department['parent_id'] : '#';
            $text = $department['name'];
            $departmentType = DepartmentType::whereId($department['department_type_id'])->first()->name;
            switch ($departmentType) {
                case '根':
                    $type = 'root';
                    $icon = 'fa fa-sitemap';
                    break;
                case '运营':
                    $type = 'company';
                    $icon = 'fa fa-building';
                    break;
                case '企业':
                    $type = 'corp';
                    $icon = 'fa fa-weixin';
                    break;
                case '学校':
                    $type = 'school';
                    $icon = 'fa fa-university';
                    break;
                case '年级':
                    $type = 'grade';
                    $icon = 'fa fa-users';
                    break;
                case '班级':
                    $type = 'class';
                    $icon = 'fa fa-user';
                    break;
                default:
                    $type = 'other';
                    $icon = 'fa fa-list';
                    break;
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
    public function getSchoolId($id) {

        $parent = $this->find($id)->parent;
        if ($parent->departmentType->name == '学校') {
            $departmentId = $parent->id;
            return School::whereDepartmentId($departmentId)->first()->id;
        } else {
            return $this->getSchoolId($parent->id);
        }

    }

    /**
     * 根据班级的部门ID获取所属年级的ID
     *
     * @param $id
     * @return int|mixed
     */
    public function getGradeId($id) {

        $parent = $this->find($id)->parent;
        if ($parent->departmentType->name == '年级') {
            $departmentId = $parent->id;
            return Grade::whereDepartmentId($departmentId)->first()->id;
        } else {
            return $this->getGradeId($parent->id);
        }

    }
    
    /**
     * 返回指定部门所处的级别
     *
     * @param integer $id 部门ID
     * @param integer $level 部门所处级别
     * @return int|null
     */
    public static function level($id, &$level) {
        
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
    public function groupLevel($userId) {

        $user = User::find($userId);
        if (!$user) { return null; }
        $group = $user->group;
        if (isset($group->school_id)) { return 'school'; }
        $topDepartmentId = $user->topDeptId($user);
        $departmentType = $this->find($topDepartmentId)->departmentType->name;
        switch ($departmentType) {
            case '根':
                return 'root';
            case '运营':
                return 'company';
            case '企业':
                return 'corp';
            default:
                return null;
        }

    }

    /**
     * 获取用户所属学校/年级/班级的学校/年级/班级ID及名称
     *
     * @param $topDepartmentId
     * @return array|null
     */
    public function topDepartment($topDepartmentId) {

        $type = $this->find($topDepartmentId)->departmentType->name;
        if (in_array($type, ['运营', '企业'])) {
            return null;
        }
        while (!in_array($type, ['学校', '年级', '班级'])) {
            $parent = $this->find($topDepartmentId)->parent;
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
    public function contacts() {
        $user = Auth::user();
        $role = $user->group->name;
        $school = new School();
//        $schoolId = $school->getSchoolId();
        $schoolId = $school->id();
        $departmentId = $school::find($schoolId)->first()->department_id;
        $contacts = [];
        if (in_array($role, $this->roles)) {
//            print_r($this->tree($departmentId));die;
            $tree = $this->tree($departmentId);
            foreach ($tree as &$t) {
                $t['seletable'] =1;
                $t['role'] ='dept';
                $t['type'] = $t['id'] == 0 ? '#' : 'dept';
                # 读取当前部门下的所有用户
                $users = $this->find($t['id'])->users;
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
            $departmentId = $this->topDeptId();
            $nodes = $this->nodes($departmentId);

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
                        if ($this->find($nodes[$i]['id'])->parent_id == $pId) {
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
                    $users = $this->find($datum['id'])->users;
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

            return response()->json(array_merge($data, $contacts));
        }

    }
    public function topDeptId() {

        $departmentIds = Auth::user()->departments
            ->pluck('id')
            ->toArray();
        $levels = [];
        foreach ($departmentIds as $id) {
            $level = 0;
            $levels[$id] = $this::level($id, $level);
        }
        asort($levels);
        reset($levels);
        $topLevelId = key($levels);
        return $this::find($topLevelId)->parent->id;

    }
    public function getPartyUser ($toparty) {
        $users = [];
        $depts = new Collection();
        foreach ($toparty as $p) {
            $this->getChildrenNode($p, $depts);
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
}

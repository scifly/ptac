<?php

namespace App\Models;

use App\Events\DepartmentCreated;
use App\Events\DepartmentMoved;
use App\Events\DepartmentUpdated;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

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
        'remark', 'order', 'enabled'
    ];
    
    /**
     * 返回所属的部门类型对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function departmentType() { return $this->belongsTo('App\Models\DepartmentType'); }
    
    /**
     * 返回对应的运营者对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function company() { return $this->hasOne('App\Models\Company'); }
    
    /**
     * 返回对应的班级对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function corp() { return $this->hasOne('App\Models\Corp'); }
    
    /**
     * 返回对应的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function school() { return $this->hasOne('App\Models\School'); }
    
    /**
     * 返回对应的年级对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function grade() { return $this->hasOne('App\Models\Grade'); }
    
    /**
     * 返回对应的班级对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function squad() { return $this->hasOne('App\Models\Squad'); }
    
    /**
     * 获取指定部门包含的所有用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users() { return $this->belongsToMany('App\Models\User', 'departments_users'); }
    
    /**
     * 返回上级部门对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
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
     * 根据schoolId和corpId返回节点
     *
     * @return Collection|static[]
     */
    private function nodes() {
        
        $nodes = $this->all();
        return $nodes;
        
    }
    
    /**
     * 获取指定部门的子部门
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
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
     * @return bool
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
     * @return bool
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
     */
    public function remove($id) {
        
        $department = $this->find($id);
        if (!$department) { return false; }
        if (!$this->removable($department)) { return false; }
        try {
            $exception = DB::transaction(function () use ($id, $department) {
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
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
        
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
        $deparment->parent_id = $parentId === '#' ? NULL : intval($parentId);
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
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree() {
        
        $departments = $this->nodes();
        $data = [];
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) ? $department['parent_id'] : '#';
            $text = $department['name'];
            $departmentType = DepartmentType::whereId($department['department_type_id'])->first()->name;
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
                'id' => $department['id'],
                'parent' => $parentId,
                'text' => $text,
                'type' => $type
            ];
        }
        return response()->json($data);
        
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
                'type' => $type
            ];
        }
        return $data;
        
    }

    public function showDepartments($ids) {

        $departments = $this->whereIn('id',$ids)->get()->toArray();
        $departmentParentIds = Array();
        $departmentUsers = Array();
        foreach ($departments as $key => $department){
            $departmentParentIds[] = $department['id'];
        }
        foreach ($departmentParentIds as $departmentId) {
            $department = Department::find($departmentId);
            foreach ($department->users as $user) {
                $departmentUsers[] = [
                    'id' => 'UserId_' . $user['id'],
                    'parent' => $departmentId,
                    'text' => $user['username'],
                    'icon' => 'fa fa-user',
                    'type' => 'user'
                ];
            }
        }
        $data = [];
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) && in_array($department['parent_id'], $departmentParentIds)? $department['parent_id'] : '#';
            $text = $department['name'];
            $departmentType = DepartmentType::whereId($department['department_type_id'])->first()->name;
            switch ($departmentType) {
                case '根': $type = 'root';  $icon = 'fa fa-sitemap'; break;
                case '运营': $type = 'company';  $icon = 'fa fa-building'; break;
                case '企业': $type = 'corp';  $icon = 'fa fa-weixin'; break;
                case '学校': $type = 'school';  $icon = 'fa fa-university'; break;
                case '年级': $type = 'grade';  $icon = 'fa fa-users'; break;
                case '班级': $type = 'class';  $icon = 'fa fa-user'; break;
                default: $type = 'other';  $icon = 'fa fa-list'; break;
            }
            $data[] = [
                'id' => $department['id'],
                'parent' => $parentId,
                'text' => $text,
                'icon' => $icon,
                'type' => $type
            ];
        }
        foreach ($departmentUsers as $departmentUser){
        $data[] = $departmentUser;
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
    
}

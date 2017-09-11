<?php

namespace App\Models;

use App\Events\DepartmentCreated;
use App\Events\DepartmentUpdated;
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
 * @property-read DepartmentType $departmentType
 * @property-read Grade $grade
 * @property-read Squad $squad
 * @method static Builder|Department whereDepartmentTypeId($value)
 */
class Department extends Model {

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
    public function users() { return $this->belongsToMany('App\Models\Department', 'departments_users'); }
    
    /**
     * 返回上级部门对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() {
        
        return $this->belongsTo('App\Models\Department', 'parent_id');
        
    }
    
    /**
     * 获取指定部门的子部门
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children() {
        
        return $this->hasMany('App\Models\Menu', 'parent_id', 'id');
        
    }
    
    /**
     * 返回所有叶节点部门
     *
     * @param array $schoolIds
     * @param array $corpIds
     * @return array
     */
    public function leaves($schoolIds = [], $corpIds = []) {
        
        $leaves = [];
        $leafPath = [];
        $departments = $this->nodes($schoolIds, $corpIds);
        foreach ($departments as $department) {
            /** @noinspection PhpUndefinedMethodInspection */
            if (empty($department->children()->count())) {
                $path = $this->leafPath($department->id, $leafPath);
                $leaves[$department->id] = $path;
                $leafPath = [];
            }
        }
        return $leaves;
        
    }
    
    /**
     * 返回Department列表
     *
     * @param array $schoolIds
     * @param array $corpIds
     * @return array
     */
    public function departments(array $schoolIds = [], array $corpIds = []) {
        
        $departments = $this->nodes($schoolIds, $corpIds);
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
            return true;
        }
        return $department ? true : false;
    
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
            return true;
        }
        return $updated ? true : false;
        
    }
    
    /**
     * 删除部门
     *
     * @param $id
     * @return bool|null
     */
    public function remove($id) {
        
        $department = $this->find($id);
        if (!isset($department)) { return false;}
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
     * @return bool
     */
    public function move($id, $parentId) {
        
        $deparment = $this->find($id);
        if (!isset($deparment)) { return false; }
        $deparment->parent_id = $parentId === '#' ? NULL : intval($parentId);
        return $deparment->save();
        
    }
    
    /**
     * 获取用于显示jstree的部门数据
     *
     * @param array $schoolIds
     * @param array $corpIds
     * @return \Illuminate\Http\JsonResponse
     */
    public function tree() {
        
        $departments = $this->nodes();
        $data = [];
        foreach ($departments as $department) {
            $parentId = isset($department['parent_id']) ? $department['parent_id'] : '#';
            $data[] = [
                'id' => $department['id'],
                'parent' => $parentId,
                'text' => $department['name']
            ];
        }
        return response()->json($data);
        
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
     * 根据schoolId和corpId返回节点
     *
     * @param array $schoolIds
     * @param array $corpIds
     * @return Collection|static[]
     */
    private function nodes(array $schoolIds = [], array $corpIds = []) {
        
        if (empty($schoolIds) && empty($corpIds)) {
            $nodes = $this::all();
        } elseif (!empty($schoolIds) && empty($corpIds)) {
            $nodes = $this->whereIn('school_id', $schoolIds)->get();
        } elseif (!empty($corpIds) && empty($schoolIds)) {
            $nodes = $this->whereIn('corp_id', $corpIds)->get();
        } else {
            $nodes = $this->whereIn('school_id', $schoolIds)
                ->whereIn('corp_id', $corpIds)->get();
        }
        return $nodes;
        
    }
    
}

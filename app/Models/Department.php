<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use App\Http\Requests\DepartmentRequest;

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
 */
class Department extends Model {

    protected $fillable = [
        'parent_id', 'corp_id', 'school_id', 'name',
        'remark', 'order', 'enabled'
    ];
    
    /**
     * 返回所属学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
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
     * 判断部门记录是否已经存在
     *
     * @param DepartmentRequest $request
     * @param null $id
     * @return bool
     */
    public function existed(DepartmentRequest $request, $id = NULL) {

        if (!$id) {
            $student = $this->where('corp_id', $request->input('corp_id'))
                ->where('school_id', $request->input('school_id'))
                ->where('parent_id', $request->input('parent_id'))
                ->where('name', $request->input('name'))
                ->first();
        } else {
            $student = $this->where('corp_id', $request->input('corp_id'))
                ->where('id', '<>', $id)
                ->where('school_id', $request->input('school_id'))
                ->where('parent_id', $request->input('parent_id'))
                ->where('name', $request->input('name'))
                ->first();
        }
        return $student ? true : false;

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
    public function tree(array $schoolIds = [], array $corpIds = []) {
        
        $departments = $this->nodes($schoolIds, $corpIds);
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

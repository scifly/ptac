<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Support\Facades\Request;

/**
 * 部门
 *
 * Class DepartmentController
 * @package App\Http\Controllers
 */
class DepartmentController extends Controller {
    
    protected $department;
    
    function __construct(Department $department) { $this->department = $department; }
    
    /**
     * 显示部门列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
    
        if (Request::method() === 'POST') {
            return $this->department->tree([1]);
        }
        return parent::output(__METHOD__);

    }
    
    /**
     * 显示创建部门记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create($id) {
        
        return $this->output(__METHOD__, ['parentId' => $id]);
        
    }
    
    /**
     * 保存新创建的部门记录
     *
     * @param DepartmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DepartmentRequest $request) {
        
        return $this->department->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的部门记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $department = $this->department->find($id);
        if (!$department) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'department' => $department,
        ]);
        
    }
    
    /**
     * 显示编辑指定部门记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $department = $this->department->find($id);
        if (!$department) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, [
            'department' => $department,
        ]);
        
    }
    
    /**
     * 更新指定的部门记录
     *
     * @param DepartmentRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DepartmentRequest $request, $id) {
        
        $department = $this->department->find($id);
        if (!$department) {
            return $this->notFound();
        }
        return $department->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的部门记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $department = $this->department->find($id);
        if (!$department) { return $this->notFound(); }
        return $department->delete() ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 更新部门所处位置
     *
     * @param $id
     * @param $parentId
     * @return \Illuminate\Http\JsonResponse
     */
    public function move($id, $parentId) {
        
        $department = $this->department->find($id);
        $parentDepartment = $this->department->find($parentId);
        if (!$department || !$parentDepartment) {
            return parent::notFound();
        }
        return $this->department->move($id, $parentId) ? parent::succeed() : parent::fail();
        
    }
    
    /**
     * 保存部门的排列顺序
     */
    public function sort() {
    
        $orders = Request::get('data');
        foreach ($orders as $id => $order) {
            $department = $this->department->find($id);
            if (isset($department)) {
                $department->order = $order;
                $department->save();
            }
        }
    
    }
    
}

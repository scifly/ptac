<?php

namespace App\Http\Controllers;

use App\Http\Requests\DepartmentRequest;
use App\Models\Department;
use Illuminate\Support\Facades\Request;

class DepartmentController extends Controller
{
    protected $department;

    function __construct(Department $department) {

        $this->department = $department;

    }


    public function index()
    {
        if (Request::get('draw')) {
            return response()->json($this->department->datatable());
        }
        return parent::output(__METHOD__);
    }

    /**
     * 显示创建部门记录的表单
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return $this->output(__METHOD__);
    }

    /**
     * 添加新部门.
     * @param DepartmentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(DepartmentRequest $request)
    {

        if ($this->department->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->department->create($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * Display the specified resource.
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $department= $this->department->find($id);
        if (!$department) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'deparment' => $department,
        ]);
    }

    /**
     * 显示编辑部门记录的表单.
     * @param $id
     * @return \Illuminate\Http\Response
     * @internal param Department $department
     */
    public function edit($id)
    {
        $department= $this->department->find($id);
        if (!$department) { return $this->notFound(); }
        return $this->output(__METHOD__, [
            'department' => $department,
        ]);
    }

    /**
     * 更新部门信息.
     * @param DepartmentRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(DepartmentRequest $request,$id)
    {
        $department = $this->department->find($id);
        if (!$department) { return $this->notFound(); }
        if ($this->department->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $department->update($request->all()) ? $this->succeed() : $this->fail();
    }

    /**
     * 删除部门.
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $department = $this->department->find($id);
        if (!$department) { return $this->notFound(); }
        return $department->delete() ? $this->succeed() : $this->fail();
    }
}

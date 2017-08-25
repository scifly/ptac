<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcedureTypeRequest;
use App\Models\ProcedureType;
use Illuminate\Support\Facades\Request;

class ProcedureTypeController extends Controller {
    
    protected $procedureType;

    function __construct(ProcedureType $procedureType) {
        
        $this->procedureType = $procedureType;
        
    }
    
    /**
     * 显示审批流程类型列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->procedureType->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建审批流程类型的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建审批流程类型的表单
     *
     * @param ProcedureTypeRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProcedureTypeRequest $request) {
        
        return $this->procedureType->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的审批流程类型记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) { return $this->notFound(); }
        return $this->output(__METHOD__, ['procedureType' => $procedureType]);
        
    }
    
    /**
     * 显示编辑指定审批流程类型的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
    
        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) { return $this->notFound(); }
        return $this->output(__METHOD__, ['procedureType' => $procedureType]);
        
    }
    
    /**
     * 更新指定的审批流程类型记录
     *
     * @param ProcedureTypeRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProcedureTypeRequest $request, $id) {
    
        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) { return $this->notFound(); }
        return $procedureType->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的审批流程类型记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) { return $this->notFound(); }
        return $procedureType->delete() ? $this->succeed() : $this->fail();
        
    }
    
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcedureRequest;
use App\Models\Procedure;
use Illuminate\Support\Facades\Request;

class ProcedureController extends Controller {
    protected $procedure;
    
    function __construct(Procedure $procedure) { $this->procedure = $procedure; }
    
    /**
     * 显示审批流程列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->procedure->datatable());
        }
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 显示创建审批流程记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的审批流程记录
     *
     * @param ProcedureRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProcedureRequest $request) {

        return $this->procedure->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的审批流程记录详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {

        $procedure = $this->procedure->find($id);
        if (!$procedure) { return $this->notFound(); }
        return $this->output(__METHOD__, ['procedure' => $procedure]);
        
    }
    
    /**
     * 显示编辑指定审批流程记录的表单
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $procedure = $this->procedure->find($id);
        if (!$procedure) { return $this->notFound(); }
        return $this->output(__METHOD__, ['procedure' => $procedure]);
        
    }
    
    /**
     * 更新指定的审批流程记录
     *
     * @param ProcedureRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProcedureRequest $request, $id) {
    
        $procedure = $this->procedure->find($id);
        if (!$procedure) { return $this->notFound(); }
        return $procedure->update($request->all()) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 删除指定的审批流程记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

        $procedure = $this->procedure->find($id);
        if (!$procedure) { return $this->notFound(); }
        return $procedure->delete() ? $this->succeed() : $this->fail();
        
    }
    
}

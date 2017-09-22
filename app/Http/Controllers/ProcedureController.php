<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcedureRequest;
use App\Models\Procedure;
use Illuminate\Support\Facades\Request;

/**
 * 审批流程
 *
 * Class ProcedureController
 * @package App\Http\Controllers
 */
class ProcedureController extends Controller {
    
    protected $procedure;
    
    function __construct(Procedure $procedure) { $this->procedure = $procedure; }
    
    /**
     * 审批流程列表
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
     * 创建审批流程
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存审批流程
     *
     * @param ProcedureRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProcedureRequest $request) {
        
        return $this->procedure->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 审批流程详情
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $procedure = $this->procedure->find($id);
        if (!$procedure) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['procedure' => $procedure]);
        
    }
    
    /**
     * 编辑审批流程
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $procedure = $this->procedure->find($id);
        if (!$procedure) {
            return $this->notFound();
        }
        return $this->output(__METHOD__, ['procedure' => $procedure]);
        
    }
    
    /**
     * 更新审批流程
     *
     * @param ProcedureRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProcedureRequest $request, $id) {
        
        $procedure = $this->procedure->find($id);
        if (!$procedure) {
            return $this->notFound();
        }
        return $procedure->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
    }
    
    /**
     * 删除审批流程
     *\
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $procedure = $this->procedure->find($id);
        if (!$procedure) {
            return $this->notFound();
        }
        return $procedure->remove($id)
            ? $this->succeed() : $this->fail();
        
    }
    
}

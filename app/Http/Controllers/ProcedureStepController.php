<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProcedureStepRequest;
use App\Models\ProcedureStep;
use Illuminate\Support\Facades\Request;

/**
 * 审批流程步骤
 *
 * Class ProcedureStepController
 * @package App\Http\Controllers
 */
class ProcedureStepController extends Controller {
    
    protected $procedureStep;
    
    function __construct(ProcedureStep $procedureStep) { $this->procedureStep = $procedureStep; }
    
    /**
     * 审批流程步骤列表
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->procedureStep->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建审批流程步骤
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存审批流程步骤
     *
     * @param ProcedureStepRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProcedureStepRequest $request) {
        
        return $this->procedureStep->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 审批流程步骤详情
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑审批流程步骤
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['procedureStep' => $procedureStep]);
        
    }
    
    /**
     * 更新流程审批步骤
     *
     * @param ProcedureStepRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProcedureStepRequest $request, $id) {
        
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) {
            return $this->notFound();
        }
        
        return $procedureStep->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除审批流程步骤
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) {
            return $this->notFound();
        }
        
        return $procedureStep->remove($id)
            ? $this->succeed() : $this->fail();
        
    }
    
}

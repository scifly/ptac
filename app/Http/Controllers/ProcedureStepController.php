<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcedureStepRequest;
use App\Models\Educator;
use App\Models\Procedure;
use App\Models\ProcedureStep;
use Illuminate\Support\Facades\Request;

class ProcedureStepController extends Controller {
    
    protected $procedureStep;
    
    function __construct(ProcedureStep $procedureStep) { $this->procedureStep = $procedureStep; }
    
    /**
     * 显示审批流程步骤列表
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
     * 显示创建审批流程步骤记录的表单
     *
     * @return bool|\Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存新创建的审批流程步骤记录
     *
     * @param ProcedureStepRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProcedureStepRequest $request) {
        
        if ($this->procedureStep->existed($request)) {
            return $this->fail('已经有此记录');
        }
        return $this->procedureStep->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 显示指定的审批流程步骤记录详情
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) { return $this->notFound(); }
        return $this->output(__METHOD__) ? $this->succeed() : $this->fail();
    
    }
    
    /**
     * 显示编辑指定审批流程步骤记录的表单
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) { return $this->notFound(); }
        return $this->output(__METHOD__, ['procedureStep' => $procedureStep]);
    }
    
    /**
     * 更新指定的流程审批步骤记录
     *
     * @param ProcedureStepRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProcedureStepRequest $request, $id) {
    
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) { return $this->notFound(); }
        if ($this->procedureStep->existed($request, $id)) {
            return $this->fail('已经有此记录');
        }
        return $procedureStep->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除指定的审批流程步骤记录
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
    
        $procedureStep = $this->procedureStep->find($id);
        if (!$procedureStep) { return $this->notFound(); }
        return $procedureStep->delete() ? $this->succeed() : $this->fail();
        
    }

    public function getSchoolEducators($id) {
        $temp = Procedure::whereId($id)->first(['school_id']);
        $data = Educator::with('user')->where('school_id', $temp->school_id)->get()->toArray();
        $educators = [];
        if (!empty($data)) {
            foreach ($data as $v) {
                $educators[$v['user_id']] = $v['user']['realname'];
            }
            return response()->json(['statusCode' => 200, 'educators' => $educators]);
        }
        return response()->json(['statusCode' => 500, 'message' => '查询失败!']);
    }
}

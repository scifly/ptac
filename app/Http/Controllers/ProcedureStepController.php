<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProcedureStepRequest;
use App\Models\ProcedureStep;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 审批流程步骤
 *
 * Class ProcedureStepController
 * @package App\Http\Controllers
 */
class ProcedureStepController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
    
    }
    
    /**
     * 审批流程步骤列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(ProcedureStep::datatable());
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建审批流程步骤
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存审批流程步骤
     *
     * @param ProcedureStepRequest $request
     * @return JsonResponse
     */
    public function store(ProcedureStepRequest $request) {
        
        return $this->result(ProcedureStep::store($request->all()));
        
    }
    
    /**
     * 审批流程步骤详情
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $ps = ProcedureStep::find($id);
        if (!$ps) { return $this->notFound(); }
        
        return $this->output();
        
    }
    
    /**
     * 编辑审批流程步骤
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $ps = ProcedureStep::find($id);
        if (!$ps) { return $this->notFound(); }
        
        return $this->output(['procedureStep' => $ps]);
        
    }
    
    /**
     * 更新流程审批步骤
     *
     * @param ProcedureStepRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProcedureStepRequest $request, $id) {
        
        $ps = ProcedureStep::find($id);
        if (!$ps) { return $this->notFound(); }
        
        return $this->result($ps->modify($request->all(), $id));
        
    }
    
    /**
     * 删除审批流程步骤
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $ps = ProcedureStep::find($id);
        if (!$ps) { return $this->notFound(); }
        
        return $this->result($ps->remove($id));
        
    }
    
}

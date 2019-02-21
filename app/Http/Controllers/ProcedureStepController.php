<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProcedureStepRequest;
use App\Models\ProcedureStep;
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
    
    protected $ps;
    
    /**
     * ProcedureStepController constructor.
     * @param ProcedureStep $ps
     */
    function __construct(ProcedureStep $ps) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->ps = $ps;
        $this->approve($ps);
        
    }
    
    /**
     * 审批流程步骤列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->ps->index()
            );
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
        
        return $this->result(
            $this->ps->store($request->all())
        );
        
    }
    
    /**
     * 编辑审批流程步骤
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'ps' => ProcedureStep::find($id),
        ]);
        
    }
    
    /**
     * 更新流程审批步骤
     *
     * @param ProcedureStepRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProcedureStepRequest $request, $id) {
        
        return $this->result(
            $this->ps->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除审批流程步骤
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->ps->remove($id)
        );
        
    }
    
}

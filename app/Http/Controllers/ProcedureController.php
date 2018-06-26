<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProcedureRequest;
use App\Models\Procedure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 审批流程
 *
 * Class ProcedureController
 * @package App\Http\Controllers
 */
class ProcedureController extends Controller {
    
    protected $procedure;
    
    function __construct(Procedure $procedure) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->procedure = $procedure;
        $this->approve($procedure);
        
    }
    
    /**
     * 审批流程列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->procedure->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建审批流程
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存审批流程
     *
     * @param ProcedureRequest $request
     * @return JsonResponse
     */
    public function store(ProcedureRequest $request) {
        
        return $this->result(
            $this->procedure->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 审批流程详情
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        return $this->output([
            'procedure' => Procedure::find($id),
        ]);
        
    }
    
    /**
     * 编辑审批流程
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'procedure' => $this->procedure->find($id),
        ]);
        
    }
    
    /**
     * 更新审批流程
     *
     * @param ProcedureRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProcedureRequest $request, $id) {
        
        return $this->result(
            $this->procedure->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除审批流程
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        return $this->result(
            $this->procedure->remove($id)
        );
        
    }
    
}

<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProcedureRequest;
use App\Models\Procedure;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
                $this->procedure->datatable()
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
        
        $this->authorize(
            'c', Procedure::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 保存审批流程
     *
     * @param ProcedureRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ProcedureRequest $request) {
        
        $this->authorize(
            'c', Procedure::class
        );
        
        return $this->result(
            $this->procedure->store($request->all())
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
        
        $procedure = Procedure::find($id);
        $this->authorize('rud', $procedure);
        
        return $this->output([
            'procedure' => $procedure,
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
        
        $procedure = Procedure::find($id);
        $this->authorize('rud', $procedure);
        
        return $this->output([
            'procedure' => $procedure,
        ]);
        
    }
    
    /**
     * 更新审批流程
     *
     * @param ProcedureRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ProcedureRequest $request, $id) {
        
        $procedure = Procedure::find($id);
        $this->authorize('rud', $procedure);
        
        return $this->result(
            $procedure->modify($request->all(), $id)
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
        
        $procedure = Procedure::find($id);
        $this->authorize('rud', $procedure);
        
        return $this->result(
            $procedure->remove($id)
        );
        
    }
    
}

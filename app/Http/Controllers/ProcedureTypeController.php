<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProcedureTypeRequest;
use App\Models\ProcedureType;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 审批流程类型
 *
 * Class ProcedureTypeController
 * @package App\Http\Controllers
 */
class ProcedureTypeController extends Controller {
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 审批流程类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                ProcedureType::datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建审批流程类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 创建审批流程类型
     *
     * @param ProcedureTypeRequest $request
     * @return JsonResponse
     */
    public function store(ProcedureTypeRequest $request) {
        
        return $this->result(
            ProcedureType::create($request->all())
        );
        
    }
    
    /**
     * 编辑审批流程类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $procedureType = ProcedureType::find($id);
        if (!$procedureType) {
            return $this->notFound();
        }
        
        return $this->output(['procedureType' => $procedureType]);
        
    }
    
    /**
     * 更新审批流程类型
     *
     * @param ProcedureTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProcedureTypeRequest $request, $id) {
        
        $pt = ProcedureType::find($id);
        if (!$pt) { return $this->notFound(); }
        
        return $this->result($pt->update($request->all()));
        
    }
    
    /**
     * 删除审批流程类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $pt = ProcedureType::find($id);
        if (!$pt) { return $this->notFound(); }
        
        return $this->result($pt->delete());
        
    }
    
}

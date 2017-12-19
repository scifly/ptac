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
    
    protected $procedureType;
    
    function __construct(ProcedureType $procedureType) {
    
        $this->middleware(['auth']);
        $this->procedureType = $procedureType;
        
    }
    
    /**
     * 审批流程类型列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->procedureType->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建审批流程类型
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建审批流程类型
     *
     * @param ProcedureTypeRequest $request
     * @return JsonResponse
     */
    public function store(ProcedureTypeRequest $request) {
        
        return $this->procedureType->create($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 编辑审批流程类型
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) {
            return $this->notFound();
        }
        
        return $this->output(__METHOD__, ['procedureType' => $procedureType]);
        
    }
    
    /**
     * 更新审批流程类型
     *
     * @param ProcedureTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProcedureTypeRequest $request, $id) {
        
        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) {
            return $this->notFound();
        }
        
        return $procedureType->update($request->all()) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除审批流程类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $procedureType = $this->procedureType->find($id);
        if (!$procedureType) {
            return $this->notFound();
        }
        
        return $procedureType->delete() ? $this->succeed() : $this->fail();
        
    }
    
}

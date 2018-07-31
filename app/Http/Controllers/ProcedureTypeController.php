<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProcedureTypeRequest;
use App\Models\ProcedureType;
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
    
    protected $pt;
    
    /**
     * ProcedureTypeController constructor.
     * @param ProcedureType $pt
     */
    function __construct(ProcedureType $pt) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->pt = $pt;
        $this->approve($pt);
        
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
                $this->pt->index()
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
            $this->pt->store($request->all())
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
        
        return $this->output([
            'pt' => ProcedureType::find($id),
        ]);
        
    }
    
    /**
     * 更新审批流程类型
     *
     * @param ProcedureTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(ProcedureTypeRequest $request, $id) {
        
        return $this->result(
            $this->pt->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除审批流程类型
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->pt->remove($id)
        );
        
    }
    
}

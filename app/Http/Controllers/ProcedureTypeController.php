<?php
namespace App\Http\Controllers;

use App\Http\Requests\ProcedureTypeRequest;
use App\Models\ProcedureType;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
    
    function __construct(ProcedureType $pt) {
    
        $this->middleware(['auth', 'checkrole']);
        $this->pt = $pt;
        
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
                $this->pt->datatable()
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
        
        $this->authorize(
            'cs', ProcedureType::class
        );
        
        return $this->output();
        
    }
    
    /**
     * 创建审批流程类型
     *
     * @param ProcedureTypeRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ProcedureTypeRequest $request) {
    
        $this->authorize(
            'cs', ProcedureType::class
        );
    
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
        
        $pt = ProcedureType::find($id);
        $this->authorize('cs', $pt);
        
        return $this->output([
            'pt' => $pt,
        ]);
        
    }
    
    /**
     * 更新审批流程类型
     *
     * @param ProcedureTypeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ProcedureTypeRequest $request, $id) {
        
        $pt = ProcedureType::find($id);
        $this->authorize('cs', $pt);
        
        return $this->result(
            $pt->modify($request->all(), $id)
        );
        
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
        $this->authorize('cs', $pt);
        
        return $this->result(
            $pt->remove($id)
        );
        
    }
    
}

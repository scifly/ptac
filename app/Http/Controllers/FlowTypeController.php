<?php
namespace App\Http\Controllers;

use App\Http\Requests\FlowTypeRequest;
use App\Models\FlowType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 审批流程
 *
 * Class FlowTypeController
 * @package App\Http\Controllers
 */
class FlowTypeController extends Controller {
    
    protected $flowType;
    
    /**
     * FlowTypeController constructor.
     * @param FlowType $flowType
     */
    function __construct(FlowType $flowType) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->flowType = $flowType;
        $this->approve($flowType);
        
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
                $this->flowType->index()
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
     * @param FlowTypeRequest $request
     * @return JsonResponse
     */
    public function store(FlowTypeRequest $request) {
        
        return $this->result(
            $this->flowType->store(
                $request->all()
            )
        );
        
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
            'flowType' => $this->flowType->find($id),
        ]);
        
    }
    
    /**
     * 更新审批流程
     *
     * @param FlowTypeRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(FlowTypeRequest $request, $id) {
        
        return $this->result(
            $this->flowType->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除审批流程
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->flowType->remove($id)
        );
        
    }
    
}

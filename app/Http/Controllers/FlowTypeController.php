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
    
    protected $ft;
    
    /**
     * FlowTypeController constructor.
     * @param FlowType $ft
     */
    function __construct(FlowType $ft) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->ft = $ft);
        
    }
    
    /**
     * 审批流程列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json($this->ft->index())
            : $this->output();
        
    }
    
    /**
     * 创建审批流程
     *
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    public function create() {
        
        return (Request::method() == 'POST' || Request::query('term'))
            ? $this->ft->step() : $this->output();
        
    }
    
    /**
     * 保存审批流程
     *
     * @param FlowTypeRequest $request
     * @return JsonResponse
     */
    public function store(FlowTypeRequest $request) {
        
        return $this->result(
            $this->ft->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑审批流程
     *
     * @param $id
     * @return bool|JsonResponse|string
     * @throws Throwable
     */
    public function edit($id) {
        
        return (Request::method() == 'POST' || Request::query('term'))
            ? $this->ft->step() : $this->output(['ft' => $this->ft->find($id)]);
        
    }
    
    /**
     * 更新审批流程
     *
     * @param FlowTypeRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(FlowTypeRequest $request, $id) {
        
        return $this->result(
            $this->ft->modify(
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
    public function destroy($id = null) {
        
        return $this->result(
            $this->ft->remove($id)
        );
        
    }
    
}

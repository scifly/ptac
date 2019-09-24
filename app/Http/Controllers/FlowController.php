<?php
namespace App\Http\Controllers;

use App\Http\Requests\FlowRequest;
use App\Models\{Media, Flow};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 流程审批
 *
 * Class FlowController
 * @package App\Http\Controllers
 */
class FlowController extends Controller {
    
    protected $flow, $media;
    
    /**
     * FlowController constructor.
     * @param Flow $flow
     * @param Media $media
     */
    function __construct(Flow $flow, Media $media) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->flow = $flow;
        $this->media = $media;
        
    }
    
    /**
     * 审批列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function index() {
    
        return Request::get('draw')
            ? response()->json($this->flow->index())
            : $this->output();
    
    }
    
    /**
     * 创建审批
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存审批
     *
     * @param FlowRequest $request
     * @return bool
     */
    function store(FlowRequest $request) {
        
        return $this->flow->store(
            $request->all()
        );
        
    }
    
    /**
     * 编辑审批
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    function edit($id) {
        
        return $this->output([
            'flow' => $this->flow->find($id)
        ]);
        
    }
    
    /**
     * 更新审批
     *
     * @param FlowRequest $request
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(FlowRequest $request, $id = null) {
        
        return $this->flow->modify(
            $request->all(), $id
        );
        
    }
    
    /**
     * 删除审批
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->flow->remove($id);
        
    }
    
}
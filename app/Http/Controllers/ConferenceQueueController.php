<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConferenceQueueRequest;
use App\Models\ConferenceQueue;
use Illuminate\Support\Facades\Request;

/**
 * 会议
 *
 * Class ConferenceQueueController
 * @package App\Http\Controllers
 */
class ConferenceQueueController extends Controller {
    
    protected $cq;
    
    function __construct(ConferenceQueue $cq) {
        
        $this->cq = $cq;
        
    }
    
    /**
     * 会议列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json($this->cq->datatable());
        }
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 创建会议
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create() {
        
        return $this->output(__METHOD__);
        
    }
    
    /**
     * 保存会议
     *
     * @param ConferenceQueueRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ConferenceQueueRequest $request) {
        
        return $this->cq->store($request->all())
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 会议详情
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        
        $cq = $this->cq->find($id);
        if (!$cq) { $this->notFound(); }
        
        return $this->output(__METHOD__, ['cq' => $cq]);
        
    }
    
    /**
     * 编辑会议
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        
        $cq = $this->cq->find($id);
        if (!$cq) { $this->notFound(); }
        
        return $this->output(__METHOD__, ['cq' => $cq]);
        
    }
    
    /**
     * 更新会议
     *
     * @param ConferenceQueueRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ConferenceQueueRequest $request, $id) {
        
        $cq = $this->cq->find($id);
        if (!$cq) { $this->notFound(); }
        
        return $this->cq->modify($request->all(), $id)
            ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除会议
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        
        $cq = $this->cq->find($id);
        if (!$cq) { $this->notFound(); }
        
        return $this->cq->remove($id)
            ? $this->succeed() : $this->fail();
        
    }
    
}

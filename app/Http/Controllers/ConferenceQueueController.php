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
    
    protected $conferenceQueue;
    
    function __construct(ConferenceQueue $conferenceQueue) {
        $this->conferenceQueue = $conferenceQueue;
        
    }
    
    /**
     * 会议列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        if (Request::get('draw')) {
            return response()->json($this->conferenceQueue->datatable());
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
        return $this->conferenceQueue->store($request) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 会议详情
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        $conferenceQueue = $this->conferenceQueue->find($id);
        if (!$conferenceQueue) {
            $this->notFound();
        }
        
        return $this->output(__METHOD__, ['conferenceQueue' => $conferenceQueue]);
        
    }
    
    /**
     * 编辑会议
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function edit($id) {
        $conferenceQueue = $this->conferenceQueue->find($id);
        if (!$conferenceQueue) {
            $this->notFound();
        }
        
        return $this->output(__METHOD__, ['conferenceQueue' => $conferenceQueue]);
    }
    
    /**
     * 更新会议
     *
     * @param ConferenceQueueRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ConferenceQueueRequest $request, $id) {
        $conferenceQueue = $this->conferenceQueue->find($id);
        if (!$conferenceQueue) {
            $this->notFound();
        }
        
        return $this->conferenceQueue->modify($request, $id) ? $this->succeed() : $this->fail();
        
    }
    
    /**
     * 删除会议
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        $conferenceQueue = $this->conferenceQueue->find($id);
        if (!$conferenceQueue) {
            $this->notFound();
        }
        
        return $this->conferenceQueue->remove($id) ? $this->succeed() : $this->fail();
        
    }
    
}

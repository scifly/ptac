<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConferenceQueueRequest;
use App\Models\ConferenceQueue;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 会议
 *
 * Class ConferenceQueueController
 * @package App\Http\Controllers
 */
class ConferenceQueueController extends Controller {
    
    
    function __construct() {
    
        $this->middleware(['auth', 'checkrole']);
        
    }
    
    /**
     * 会议列表
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                ConferenceQueue::datatable()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建会议
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存会议
     *
     * @param ConferenceQueueRequest $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function store(ConferenceQueueRequest $request) {
        
        $this->authorize('c', ConferenceQueue::class);
        
        return $this->result(
            ConferenceQueue::store($request->all())
        );
        
    }
    
    /**
     * 会议详情
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function show($id) {
        
        $cq = ConferenceQueue::find($id);
        $this->authorize('eud', $cq);
        
        return $this->output(['cq' => $cq]);
        
    }
    
    /**
     * 编辑会议
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        $cq = ConferenceQueue::find($id);
        $this->authorize('rud', $cq);
        
        return $this->output(['cq' => $cq]);
        
    }
    
    /**
     * 更新会议
     *
     * @param ConferenceQueueRequest $request
     * @param $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function update(ConferenceQueueRequest $request, $id) {
        
        $cq = ConferenceQueue::find($id);
        $this->authorize('eud', $cq);
        
        return $this->result(
            $cq::modify($request->all(), $id)
        );
        
    }
    
    /**
     * 删除会议
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function destroy($id) {
        
        $cq = ConferenceQueue::find($id);
        $this->authorize('eud', $cq);
        
        return $this->result($cq->remove($id));
        
    }
    
}

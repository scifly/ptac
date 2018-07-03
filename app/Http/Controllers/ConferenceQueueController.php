<?php
namespace App\Http\Controllers;

use App\Http\Requests\ConferenceQueueRequest;
use App\Models\ConferenceQueue;
use Exception;
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
    
    protected $cq;
    
    /**
     * ConferenceQueueController constructor.
     * @param ConferenceQueue $cq
     */
    function __construct(ConferenceQueue $cq) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->cq = $cq;
        $this->approve($cq);
        
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
                $this->cq->index()
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
     */
    public function store(ConferenceQueueRequest $request) {
        
        return $this->result(
            $this->cq->store($request->all())
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
        
        return $this->output([
            'cq' => $this->cq->find($id),
        ]);
        
    }
    
    /**
     * 编辑会议
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'cq' => $this->cq->find($id),
        ]);
        
    }
    
    /**
     * 更新会议
     *
     * @param ConferenceQueueRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function update(ConferenceQueueRequest $request, $id) {
        
        return $this->result(
            $this->cq->modify(
                $request->all(), $id
            )
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
        
        return $this->result(
            $this->cq->remove($id)
        );
        
    }
    
}

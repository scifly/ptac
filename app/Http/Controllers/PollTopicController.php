<?php
namespace App\Http\Controllers;

use App\Http\Requests\PollTopicRequest;
use App\Models\PollTopic;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 问卷题目
 *
 * Class PollTopicController
 * @package App\Http\Controllers
 */
class PollTopicController extends Controller {
    
    protected $topic;
    
    /**
     * PollTopicController constructor.
     * @param PollTopic $topic
     */
    function __construct(PollTopic $topic) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->topic = $topic);
        
    }
    
    /**
     * 题目列表
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->topic->index()
            );
        }
        
        return $this->output();
        
    }
    
    /**
     * 创建题目
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function create() {
        
        return $this->output();
        
    }
    
    /**
     * 保存题目
     *
     * @param PollTopicRequest $request
     * @return JsonResponse
     */
    public function store(PollTopicRequest $request) {
        
        return $this->result(
            $this->topic->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑题目
     *
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'topic' => $this->topic->find($id),
        ]);
        
    }
    
    /**
     * 更新题目
     *
     * @param PollTopicRequest $request
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function update(PollTopicRequest $request, $id) {
        
        return $this->result(
            $this->topic->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 删除题目
     *
     * @param $id
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy($id) {
        
        return $this->result(
            $this->topic->remove($id)
        );
        
    }
    
}

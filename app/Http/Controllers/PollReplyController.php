<?php
namespace App\Http\Controllers;

use App\Http\Requests\PollReplyRequest;
use App\Models\PollReply;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 问卷调查结果
 *
 * Class PollReplyController
 * @package App\Http\Controllers
 */
class PollReplyController extends Controller {
    
    protected $reply;
    
    /**
     * PollReplyController constructor.
     * @param PollReply $reply
     */
    function __construct(PollReply $reply) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->approve($this->reply = $reply);
        
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
                $this->reply->index()
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
     * @param PollReplyRequest $request
     * @return JsonResponse
     */
    public function store(PollReplyRequest $request) {
        
        return $this->result(
            $this->reply->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑题目
     * @param $id
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function edit($id) {
        
        return $this->output([
            'topic' => $this->reply->find($id),
        ]);
        
    }
    
    /**
     * 更新题目
     *
     * @param PollReplyRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function update(PollReplyRequest $request, $id) {
        
        return $this->result(
            $this->reply->modify(
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
            $this->reply->remove($id)
        );
        
    }
    
}

<?php
namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\Department;
use App\Models\Message;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Throwable;

/**
 * 消息中心
 *
 * Class MessageController
 * @package App\Http\Controllers
 */
class MessageController extends Controller {
    
    protected $msg, $dept;
    
    /**
     * MessageController constructor.
     * @param Message $msg
     * @param Department $dept
     */
    public function __construct(Message $msg, Department $dept) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->dept = $dept;
        $this->approve($this->msg = $msg);
    
    }
    
    /**
     * 消息中心
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        return Request::get('draw')
            ? response()->json(
                $this->msg->index()
            )
            : (Request::method() == 'POST'
                ? (Request::has('file')
                    ? $this->msg->import()
                    : $this->dept->contacts()
                )
                : $this->output()
            );
        
    }
    
    /**
     * 保存消息（草稿）
     *
     * @param MessageRequest $request
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function store(MessageRequest $request) {
        
        return $this->result(
            $this->msg->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 编辑消息（草稿）
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function edit($id) {
        
        return response()->json(
            $this->msg->edit($id)
        );
        
    }
    
    /**
     * 更新消息（草稿）
     *
     * @param MessageRequest $request
     * @param null $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function update(MessageRequest $request, $id = null) {
        
        return $this->result(
            $this->msg->modify(
                $request->all(), $id
            )
        );
        
    }
    
    /**
     * 消息详情
     *
     * @param $id
     * @return Factory|View
     * @throws Throwable
     */
    public function show($id) {
        
        return $this->msg->show($id);
        
    }
    
    /**
     * 发送消息
     *
     * @param MessageRequest $request
     * @return bool|JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function send(MessageRequest $request) {
        
        return $this->result(
            $this->msg->send($request->all()),
            !$request->has('preview')
                ? __('messages.message.submitted')
                : __('messages.message.preview'),
            __('messages.message.failed')
        );
        
    }
    
    /**
     * 删除消息
     *
     * @param null $id
     * @return JsonResponse|string
     * @throws Throwable
     */
    public function destroy($id = null) {
        
        return $this->result(
            $this->msg->remove($id)
        );
        
    }
    
}
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
    
    protected $message, $department;
    
    /**
     * MessageController constructor.
     * @param Message $message
     * @param Department $departement
     */
    public function __construct(
        Message $message, Department $departement
    ) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->message = $message;
        $this->department = $departement;
        
    }
    
    /**
     * 消息中心
     *
     * @return bool|JsonResponse
     * @throws Throwable
     */
    public function index() {
        
        if (Request::get('draw')) {
            return response()->json(
                $this->message->index()
            );
        }
        if (Request::method() == 'POST') {
            return Request::has('file')
                ? $this->message->upload()
                : $this->department->contacts();
        }
        
        return $this->output();
        
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
            $this->message->store(
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
            $this->message->edit($id)
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
            $this->message->modify(
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
    
        return $this->message->show($id);
        
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
            $this->message->send($request->all()),
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
            $this->message->remove($id)
        );
    
    }
    
}
<?php
namespace App\Http\Controllers;

use App\Http\Requests\MessageRequest;
use App\Models\Department;
use App\Models\Media;
use App\Models\Message;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Throwable;

/**
 * 消息中心
 *
 * Class MessageController
 * @package App\Http\Controllers
 */
class MessageController extends Controller {
    
    protected $message, $department, $user, $media;
    
    /**
     * MessageController constructor.
     * @param Message $message
     * @param Department $departement
     * @param User $user
     * @param Media $media
     */
    public function __construct(
        Message $message, Department $departement,
        User $user, Media $media
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
     * 编辑消息
     *
     * @param $id
     * @return JsonResponse
     */
    public function edit($id) {
    
        list($content) = $this->message->show($id);
        
        return response()->json($content);
    
    }
    
    /**
     * 发送消息
     *
     * @param MessageRequest $request
     * @return bool|JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function store(MessageRequest $request) {
        
        return $this->result(
            $this->message->send($request->all()),
            __('messages.message.submitted'),
            __('messages.message.failed')
        );
        
    }
    
    /**
     * 标记已读.未读
     *
     * @throws Exception
     */
    public function update() {
    
        return $this->result(
            $this->message->modify()
        );
    
    }
    
    /**
     * 删除消息
     * @param null $id
     * @return JsonResponse|string
     * @throws Exception
     */
    public function destroy($id = null) {
    
        return $this->result(
            $this->message->remove($id)
        );
    
    }
    
}
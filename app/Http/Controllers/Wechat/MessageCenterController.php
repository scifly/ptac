<?php
namespace App\Http\Controllers\Wechat;

use App\Helpers\HttpStatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Media;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Throwable;

/**
 * Class MessageCenterController
 * @package App\Http\Controllers\Wechat
 */
class MessageCenterController extends Controller {
    
    protected $message, $user, $department, $media, $student, $mr, $du;
    
    /**
     * MessageCenterController constructor.
     * @param Message $message
     * @param User $user
     * @param Department $department
     * @param Media $media
     * @param Student $student
     * @param MessageReply $mr
     * @param DepartmentUser $du
     */
    public function __construct(
        Message $message, User $user,
        Department $department, Media $media,
        Student $student, MessageReply $mr,
        DepartmentUser $du
    ) {
        
        $this->middleware('wechat');
        $this->message = $message;
        $this->user = $user;
        $this->department = $department;
        $this->media = $media;
        $this->student = $student;
        $this->mr = $mr;
        $this->du = $du;
        
    }
    
    /**
     * 消息列表
     *
     * @return array|RedirectResponse|Redirector|View|string
     * @throws Throwable
     */
    public function index() {
        
        return $this->message->wIndex();
        
    }
    
    /**
     * 创建消息
     *
     * @return bool|Factory|JsonResponse|RedirectResponse|Redirector|View
     * @throws Throwable
     */
    public function create() {
        
        if (Request::method() == 'POST') {
            return Request::has('file')
                ? $this->message->upload()
                : response()->json(
                    $this->message->search()
                );
        }
        
        return view('wechat.message_center.create');
        
    }
    
    /**
     * 保存并发送消息
     *
     * @param MessageRequest $request
     * @return bool|JsonResponse|RedirectResponse|Redirector
     * @throws Throwable
     */
    public function store(MessageRequest $request) {
        
        $sent = $this->message->send(
            $request->all()
        );
        
        return response()->json([
            'message' => $sent ? __('messages.ok') : __('messages.message.failed'),
        ], $sent ? HttpStatusCode::OK : HttpStatusCode::INTERNAL_SERVER_ERROR);
        
    }
    
    /**
     * 消息编辑页面
     *
     * @param $id
     * @return bool|Factory|RedirectResponse|Redirector|View
     */
    public function edit($id) {
        
        $message = $this->message->find($id);
        abort_if(
            !$message,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return view('wechat.message_center.create', [
            'message' => $message,
        ]);
        
    }
    
    /**
     * 更新已读状态
     *
     * @param $id
     * @return bool|JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     * @throws Throwable
     */
    public function read($id) {
        
        $this->message->read($id);
        
        return response()->json([
            'message' => __('messages.ok'),
        ]);
        
    }
    
    /**
     * 消息详情页面展示
     *
     * @param $id
     * @return Factory|View
     */
    public function show($id) {
        
        list($content, $edit) = $this->message->show($id);

        return view('wechat.message_center.show', [
            'content' => $content,
            'edit'    => $edit,
            'show'    => true,
        ]);
        
    }
    
    /**
     * 删除指定消息
     *
     * @param $id
     * @return bool|JsonResponse|null
     * @throws Exception
     */
    public function destroy($id) {
        
        $message = $this->message->find($id);
        abort_if(
            !$message,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $deleted = $message->delete();
        
        return response()->json([
            'message' => $deleted ? __('messages.ok') : __('messages.fail'),
        ], $deleted ? HttpStatusCode::OK : HttpStatusCode::INTERNAL_SERVER_ERROR);
        
    }
    
    /**
     * 消息回复列表
     *
     * @return JsonResponse
     */
    public function replies() {
        
        return $this->message->replies();
        
    }
    
    /**
     * 消息回复
     *
     */
    public function reply() {
        
        $input = Request::all();
        $input['user_id'] = Auth::id();
        $replied = $this->mr->store($input);
        
        return response()->json([
            'message' => $replied ? __('messages.ok') : __('messages.fail'),
        ], $replied ? HttpStatusCode::OK : HttpStatusCode::INTERNAL_SERVER_ERROR);
        
    }
    
    /**
     * 删除指定的消息回复
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function remove($id) {
        
        $mr = MessageReply::find($id);
        abort_if(
            !$mr, HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $removed = $mr->delete();
        
        return response()->json([
            'message' => $removed ? __('messages.del_ok') : __('messages.del_fail'),
        ], $removed ? HttpStatusCode::OK : HttpStatusCode::INTERNAL_SERVER_ERROR);
        
    }
    
}
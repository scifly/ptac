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
     * 保存消息（草稿）
     *
     * @param MessageRequest $request
     * @return JsonResponse
     */
    public function store(MessageRequest $request) {
        
        return $this->result(
            $this->message->store(
                $request->all()
            )
        );
        
    }
    
    /**
     * 保存并发送消息
     *
     * @param MessageRequest $request
     * @return bool|JsonResponse|RedirectResponse|Redirector
     * @throws Throwable
     */
    public function send(MessageRequest $request) {
        
        return $this->result(
            $this->message->send(
                $request->all()
            )
        );
        
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
        
        return $this->result(
            $this->message->read($id)
        );
        
    }
    
    /**
     * 消息详情页面展示
     *
     * @param $id
     * @return Factory|View
     * @throws Throwable
     */
    public function show($id) {
        
        return $this->message->show(
            $id, true
        );
        
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
        
        return $this->result(
            $message->delete()
        );
        
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
     * @return JsonResponse
     */
    public function reply() {
        
        $input = Request::all();
        $input['user_id'] = Auth::id();
        
        return $this->result(
            $this->mr->store($input)
        );
        
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
        
        return $this->result(
            $mr->delete()
        );
        
    }
    
}
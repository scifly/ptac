<?php
namespace App\Http\Controllers\Wechat;

use Exception;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Facades\Wechat;
use App\Helpers\HttpStatusCode;
use App\Helpers\WechatTrait;
use App\Http\Controllers\Controller;
use App\Http\Requests\MessageRequest;
use App\Models\App;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Media;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\MessageSendingLog;
use App\Models\Student;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class MessageCenterController extends Controller {
    
    use WechatTrait;
    
    const APP = '消息中心';
    
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
        
        return Auth::id()
            ? $this->message->wIndex()
            : $this->signin(self::APP, Request::url());
        
    }
    
    /**
     * 创建消息
     *
     * @return Factory|JsonResponse|View
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
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(MessageRequest $request) {
        
        $sent = $this->message->send(
            $request->all()
        );
        
        return response()->json([
            'message' => $sent ? __('messages.ok') : __('messages.message.failed')
        ], $sent ? HttpStatusCode::OK : HttpStatusCode::INTERNAL_SERVER_ERROR);
        
    }
    
    /**
     * 消息编辑页面
     *
     * @param $id
     * @return Factory|View
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
     * @return JsonResponse
     * @throws Exception
     * @throws Throwable
     */
    public function read($id) {

        $this->message->read($id);
        
        return response()->json([
            'message' => __('messages.ok')
        ]);
        
    }
    
    /**
     * 消息详情页面展示
     *
     * @param $id
     * @return Factory|View
     */
    public function show($id) {
        
        $user = Auth::user();
        $message = $this->message->find($id);
        $edit = ($user->id == $message->s_user_id ? true : false);
        
        return view('wechat.message_center.show', [
            'message' => $message,
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
        
        //只能删除查看的记录 不能删除多媒体文件 多媒体文件路径被多个记录存入
        return $this->result(
            $message->delete()
        );
        
    }
    
    /**
     * 消息回复
     *
     */
    public function reply() {
        
        $userId = Session::get('userId');
        $user = $this->user->where('userid', $userId)->first();
        $input = Request::all();
        $input['user_id'] = $user->id;
        
        return $this->result(
            $this->mr->store($input)
        );
        
    }
    
    /**
     * 消息回复列表
     *
     * @return JsonResponse
     */
    public function replyList() {
        
        $user = Auth::user();
        $input = Request::all();
        $message = $this->message->find($input['id']);
        $lists = MessageReply::where('msl_id', $input['msl_id'])->get();
        if ($user->id == $message->s_user_id) {
            foreach ($lists as $list) {
                $list->name = $list->user->realname;
            }
        } else {
            $lists = MessageReply::where('msl_id', $input['msl_id'])
                ->where('user_id', $user->id)->get();
            foreach ($lists as $list) {
                $list->name = $list->user->realname;
            }
        }
        
        return $this->result($lists, $lists);
        
    }
    
    /**
     * 消息回复删除
     *
     * @param $id
     * @return JsonResponse
     * @throws Exception
     */
    public function replyDestroy($id) {
        
        $mr = MessageReply::find($id);
        abort_if(!$mr, HttpStatusCode::NOT_FOUND);
        
        return $this->result(
            $mr->delete()
        );
        
    }
    
    /**
     *上传图片和视频
     *
     * @return JsonResponse
     */
    public function upload() {
        
        $type = Request::input('type');
        if (empty($type)) {
            $data = $this->media->upload(Request::file('file'), '前端消息中心');
            
            return $this->result($data, $data);
        }
        if ($type == 'mpnews') {
            $type = 'image';
        }
        $file = Request::file('file');
        if (empty($file)) {
            abort(HttpStatusCode::NOT_ACCEPTABLE, '您还未选择文件！');
        } else {
            $result['data'] = [];
            $mes = $this->media->upload($file, ' 前端消息中心');
            if ($mes) {
                $this->result['message'] = '上传成功！';
                $path = $mes['path'];
                $data = ["media" => curl_file_create($path)];
                $crop = Corp::whereName('万浪软件')->first();
                $app = App::whereAgentid('999')->first();
                $token = Wechat::getAccessToken($crop->corpid, $app->secret);
                $status = Wechat::uploadMedia($token, $type, $data);
                $message = json_decode($status);
                if ($message->errcode == 0) {
                    $mes['media_id'] = $message->media_id;
                    $this->result['data'] = $mes;
                } else {
                    abort(HttpStatusCode::INTERNAL_SERVER_ERROR, '微信服务器上传失败！');
                }
            } else {
                abort(HttpStatusCode::INTERNAL_SERVER_ERROR, '文件上传失败！');
            }
        }
        
        return response()->json($this->result);
        
    }
    
}
<?php
namespace App\Http\Controllers\Wechat;

use Exception;
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
     * @return RedirectResponse|Redirector|View|string
     * @throws Throwable
     */
    public function index() {
        
        if (!Auth::id()) {
            return $this->signin(self::APP);
        }
        if (Request::method() == 'POST') {
            return response()->json(
                $this->message->search()
            );
        }
        
        return view('wechat.message_center.index');
        
    }
    
    /**
     * 发送消息页面
     *
     * @param $departmentId
     * @return Factory|View
     * @throws Throwable
     */
    public function create($departmentId = null) {
        
        if (Request::method() == 'POST') {
            return response()->json(
                $this->message->search($departmentId)
            );
        }
        
        return view('wechat.message_center.create');
        
    }
    
    /**
     * 发送消息
     *
     * @param MessageRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function store(MessageRequest $request) {
        
        return $this->result(
            $this->message->store($request)
        );
        
    }
    
    /**
     * 前端应用消息推送 微信端
     *
     * @param $input
     * @param null $url
     * @return bool
     */
    private function frontSendMessage($input, $url = null) {
        
        $corpId = 'wxe75227cead6b8aec';
        $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
        $token = Wechat::getAccessToken($corpId, $secret);
        $agentid = 3;
        $users = [];
        foreach ($input['user_ids'] as $u_id) {
            $users[] = User::find($u_id)->userid;
        }
        if (!empty($input['department_ids'])) {
            $toparty = implode('|', $input['department_ids']);
        } else {
            $toparty = '';
        }
        $topuser = implode('|', $users);
        $message = [
            'touser'  => $topuser,
            'toparty' => $toparty,
            'agentid' => $agentid,
        ];
        switch ($input['type']) {
            case 'text' :
                $message['text'] = ['content' => $input['content']];
                break;
            case 'textcard':
                $message['textcard'] = [
                    'title'       => $input['title'],
                    'description' => strip_tags($input['content']),
                    'url'         => $url,
                ];
                break;
            case 'mpnews' :
                $message['mpnews']['articles'] =
                    [
                        [
                            'title'              => $input['title'],
                            'thumb_media_id'     => $input['mediaid'],
                            'content'            => $input['content'],
                            'content_source_url' => $url,
                            'digest'             => strip_tags($input['content']),
                        ],
                    ];
                break;
            case 'image' :
                $message['image'] = ['media_id' => $input['mediaid']];
                break;
            case 'video' :
                $message['video'] = [
                    'media_id'    => $input['mediaid'],
                    'title'       => $input['title'],
                    'description' => strip_tags($input['content']),
                ];
                break;
        }
        $message['msgtype'] = $input['type'];
        $status = json_decode(Wechat::sendMessage($token, $message));
        
        return $status->errcode == 0;
        
    }
    
    /**
     * 消息编辑页面
     *
     * @param $id
     * @return Factory|View
     */
    public function edit($id) {
        
        $message = $this->message->find($id);
        abort_if(!$message, HttpStatusCode::NOT_FOUND);
        
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
    public function updateStatus($id) {
        
        //操作 msl表 和 message表 暂时放在控制器
        return $this->result(
            $this->modifyReaded($id)
        );
        
    }
    
    /**
     * 更新是否已读并且更新对应msl记录
     *
     * @param $id
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    private function modifyReaded($id) {
        
        $message = $this->message->find($id);
        abort_if(!$message, HttpStatusCode::NOT_FOUND);
        try {
            DB::transaction(function () use ($message, $id) {
                $message->read = 1;
                $message->save();
                $msl = MessageSendingLog::whereId($message->msl_id)->first();
                $msl->read_count = $msl->read_count + 1;
                
                return $msl->save() ? true : false;
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
    }
    
    /**
     * 消息详情页面展示
     *
     * @param $id
     * @return Factory|View
     */
    public function show($id) {
        
        $userId = Session::get('userId');
        $user = $this->user->where('userid', $userId)->first();
        $message = $this->message->find($id);
        if (json_decode($message->content) != null) {
            $content = json_decode($message->content, true);
            if (array_key_exists("content", $content)) {
                $message->content = $content['content'];
            } elseif (array_key_exists("articles", $content)) {
                $message->content = $content['articles'][0]['content'];
            } else {
                $message->content = '';
            }
        }
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
        abort_if(!$message, HttpStatusCode::NOT_FOUND);
        
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
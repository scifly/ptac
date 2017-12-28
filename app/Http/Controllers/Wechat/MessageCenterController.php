<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Helpers\ControllerTrait;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Media;
use App\Models\Message;
use App\Models\MessageSendingLog;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class MessageCenterController extends Controller {
    
    use ControllerTrait;
    
    protected $message, $user;
    
    /**
     * MessageCenterController constructor.
     * @param Message $message
     * @param User $user
     */
    public function __construct(Message $message, User $user) {
        // $this->middleware();
        $this->message = $message;
        $this->user = $user;
        
    }
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * @throws \Throwable
     */
    public function index() {
        #获取用户信息
        // $corpId = 'wxe75227cead6b8aec';
        // $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
        // $agentId = 3;
        // $code = Request::input('code');
        // if (empty($code)) {
        //     $codeUrl = Wechat::getCodeUrl($corpId, $agentId, 'http://weixin.028lk.com/message_center');
        //
        //     return redirect($codeUrl);
        // } else {
        //     $code = Request::get('code');
        //     $accessToken = Wechat::getAccessToken($corpId, $secret);
        //     $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
        // }
        // $userId = $userInfo['UserId'];
        $userId = 'yuanhongbin';
        $user = User::whereUserid($userId)->first();
        if (Request::isMethod('post')) {
            $keywords = Request::get('keywords');
            $type = Request::get('type');
            if (!empty($keywords)) {
                switch ($type) {
                    case 'send':
                        $sendMessages = [];
                        $sendMessages = Message::whereSUserId($user->id)
                            ->Where('content', 'like', '%' . $keywords . '%')
                            ->orWhere('title', 'like', '%' . $keywords . '%')
                            ->get();
                        if (sizeof($sendMessages) != 0) {
                            foreach ($sendMessages as $s) {
                                $s['r_user_id'] = User::whereId($s['r_user_id'])->first()->realname;
                            }
                        }
                        
                        return response(['sendMessages' => $sendMessages, 'type' => $type]);
                        break;
                    case 'receive':
                        $receiveMessages = [];
                        $receiveMessages = Message::whereRUserId($user->id)
                            ->where('content', 'like', '%' . $keywords . '%')
                            ->orWhere('title', 'like', '%' . $keywords . '%')
                            ->get();
                        if (sizeof($receiveMessages) != 0) {
                            foreach ($receiveMessages as $r) {
                                $r['s_user_id'] = User::whereId($r['s_user_id'])->first()->realname;
                            }
                        }
                        
                        return response(['type' => $type, 'receiveMessages' => $receiveMessages]);
                        break;
                    default:
                        break;
                }
                
            }
            
        }
        //判断是否为教职工
        $educator = false;
        if($user->group->name == '教职员工'){
            $educator = true;
        }
        
        $sendMessages = $this->message->where('s_user_id', $user->id)->get()->unique('msl_id')->groupBy('message_type_id');
        $receiveMessages = $this->message->where('r_user_id', $user->id)->get()->groupBy('message_type_id');
       
        $count = $this->message->where('r_user_id', $user->id)->where('readed', '0')->count();
        
        return view('wechat.message_center.index', [
            'receiveMessages' => $receiveMessages,
            'sendMessages'    => $sendMessages,
            'count'           => $count,
            'educator'        => $educator,
        ]);
    }
    
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        $corpId = 'wxe75227cead6b8aec';
        $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
        $agentId = 3;
        $code = Request::input('code');
        if (empty($code)) {
            $codeUrl = Wechat::getCodeUrl($corpId, $agentId, 'http://weixin.028lk.com/message_create');
        
            return redirect($codeUrl);
        } else {
            $code = Request::get('code');
            $accessToken = Wechat::getAccessToken($corpId, $secret);
            $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
        }
        
        
        $userId = $userInfo['UserId'];
        // $userId = "yuanhongbin";
        #教师可发送消息
        $user = $this->user->where('userid', $userId)->first();
        #取的和教师关联的学校的部门id
        print_r($user->departments);
        die;
        $departmentId = $user->departments()->where('department_type_id',4)->first()->id;
       
        $departments = Department::where('parent_id', $departmentId)->get();
        $department = Department::whereId($departmentId)->first();
        $users = $department->users;
        
        return view('wechat.message_center.create', ['departments' => $departments, 'users' => $users]);
    }
    
    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     * @throws \Throwable
     */
    public function store() {
        
        return $this->frontStore() ? $this->succeed() : $this->fail();
    }
    
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit($id) {
        
        $message = $this->message->find($id);
        if (!$message) {
            return $this->notFound();
        }
        
        return view('wechat.message_center.create', ['message' => $message]);
    }
    
    /**
     * @param $id
     */
    public function update($id) {
    
    }
    
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \Throwable
     */
    public function updateStatus($id) {
        //操作 msl表 和 message表 暂时放在控制器
        return $this->modifyReaded($id) ? $this->succeed() : $this->fail();
    }
    
    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id) {
        // $userId = $this->getRole('http://weixin.028lk.com/message_show');
        $userId = "yuanhongbin";
        $user = $this->user->where('userid', $userId)->first();
        $message = $this->message->find($id);
        $edit = $user->id == $message->s_user_id ? true : false;
        
        return view('wechat.message_center.show', ['message' => $this->message->find($id), 'edit' => $edit, 'show' => true]);
    }
    
    /**
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse|null
     * @throws \Exception
     */
    public function destory($id) {
        $message = $this->message->find($id);
        if (!$message) {
            return $this->notFound();
        }
        
        //只能删除查看的记录 不能删除多媒体文件 多媒体文件路径被多个记录存入
        return $message->delete() ? $this->succeed() : $this->fail();
    }
    
    /**
     *
     * @param null $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload($id = null) {
        if ($id) {
            //删除已上传的图片
            $media = Media::whereId($id)->first();
            if ($media->path) {
                $removeFile = public_path('uploads/') . $media->path;
                if (is_file($removeFile)) {
                    unlink($removeFile);
                }
            }
            
            return $media->delete() ? $this->succeed() : $this->fail();
        }
        //上传图片
        $data = $this->uploadedMedias(Request::file('file'), '前端消息中心');
        
        return $data ? $this->succeed($data) : $this->fail();
    }
    
    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getNextDept($id) {
        
        $department = Department::whereId($id)->first();
        $users = $department->users;
        $nextDepts = Department::where('parent_id', $id)->get();
        $data = view('wechat.message_center.select', ['departments' => $nextDepts, 'users' => $users])->render();
        
        return $data ? $this->succeed($data) : $this->fail();
        
    }
    
    // /**
    //  * @param $calbackUrl
    //  * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
    //  *
    //  */
    // private function getRole($calbackUrl) {
    //     //获取微信用户信息
    //     $corpId = 'wxe75227cead6b8aec';
    //     $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
    //     $agentId = 3;
    //     $code = Request::input('code');
    //     if (empty($code)) {
    //         $codeUrl = Wechat::getCodeUrl($corpId, $agentId, $calbackUrl);
    //
    //         return redirect($codeUrl);
    //     } else {
    //         $code = Request::get('code');
    //         $accessToken = Wechat::getAccessToken($corpId, $secret);
    //         $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
    //         return $userInfo['UserId'];
    //     }
    // }
    
    /**
     * 更新是否已读并且更新对应msl记录
     *
     * @param $id
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    private function modifyReaded($id) {
        $message = $this->message->find($id);
        if (!$message) {
            return false;
        }
        try {
            DB::transaction(function () use ($message, $id) {
                $message->readed = 1;
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
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    private function frontStore() {
        $userId = "yuanhongbin";
        $user = $this->user->where('userid', $userId)->first();
        $input = Request::all();
        $userIds = [];
        #处理接收者 这里先处理了一层
        if (!empty($input['department_ids'])) {
            foreach ($input['department_ids'] as $departmentId) {
                $department = Department::whereId($departmentId)->first();
                $users = $department->users;
                foreach ($users as $user) {
                    $userIds[] = $user->id;
                }
            }
        }
        $receiveUserIds = array_merge($input['user_ids'], $userIds);
        try {
            DB::transaction(function () use ($receiveUserIds, $input, $user) {
                $messageSendingLog = new MessageSendingLog();
                #新增一条日志记录（指定批次）
                $sendLogData = [
                    'read_count' => 0,
                    'received_count' => 0,
                    'recipient_count' => count($receiveUserIds),
                ];
                $input['msl_id'] = $messageSendingLog->create($sendLogData)->id;
                $msl = $messageSendingLog->whereId($input['msl_id'])->first();
                if (isset($input['media_ids'])){
                $input['media_ids'] = implode(',', $input['media_ids']);
                } else {
                    $input['media_ids'] = '0';
                }
                foreach ($receiveUserIds as $receiveUserId) {
                    $messageData = [
                        'title'           => $input['title'],
                        'comm_type_id'    => 1,
                        'app_id'          => 1,
                        'msl_id'          => $input['msl_id'],
                        'content'         => $input['content'],
                        'serviceid'       => 0,
                        'message_id'      => 0,
                        'url'             => '0',
                        'media_ids'       => $input['media_ids'],
                        's_user_id'       => $user->id,
                        'r_user_id'       => $receiveUserId,
                        'message_type_id' => 1,
                        'readed'          => 0,
                        'sent'            => 0,
                    ];
                    $message = $this->message->create($messageData);
                    $message->sent = 1;
                    $message->save();
                    #更新msl表
                    $msl->received_count = $msl->received_count + 1;
                    $msl->save();
                }
                #推送微信服务器且显示详情页
                $message = $this->message->where('msl_id',$input['msl_id'])->first();
                $url = 'http://weixin.028lk.com/message_show/' . $message->id;
                $this->frontSendMessage($input, $url);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
    }
    
    /**
     * 前端消息推送
     *
     * @param $input
     * @param null $url
     * @return bool
     */
    private function frontSendMessage($input, $url = null) {
        // if(empty($input['media_ids'])){
        //     $input['type'] = 'text';
        // }
        $input['type'] = 'textcard';
        $corpId = 'wxe75227cead6b8aec';
        $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
        $token = Wechat::getAccessToken($corpId, $secret, $url);
        $agentid = 3;
        $users = [];
        foreach ($input['user_ids'] as $u_id){
            $users[] = User::find($u_id)->userid;
        }
        if(!empty($input['department_ids'])) {
            $toparty = implode('|', $input['department_ids']);
        } else {
            $toparty = '';
        }
        $topuser= implode('|', $users);
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
                    'title' => $input['title'],
                    'description' => strip_tags($input['content']),
                    'url' => $url
                ];
                break;
            // case 'image' :
            //
            // case 'voice' :
            //     $message['image'] = ['media_id' => $data['content']['media_id']];
            //     break;
            // case 'mpnews' :
            //     $message['mpnews'] = ['articles' => $data['content']['articles']];
            //     break;
            // case 'video' :
            //     $message['video'] = $data['content']['video'];
            //     break;
        }
        $message['msgtype'] = $input['type'];
        $status = json_decode(Wechat::sendMessage($token, $message));
        return $status->errcode == 0 ? true : false;
    }
}

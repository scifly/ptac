<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Helpers\ControllerTrait;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\CommType;
use App\Models\Corp;
use App\Models\Department;
use App\Models\Educator;
use App\Models\Media;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\MessageSendingLog;
use App\Models\MessageType;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Session;

class MessageCenterController extends Controller {
    
    use ControllerTrait;
    
    protected $message, $user, $department;
    
    /**
     * MessageCenterController constructor.
     * @param Message $message
     * @param User $user
     * @param Department $department
     */
    public function __construct(Message $message, User $user, Department $department) {
        // $this->middleware();
        $this->message = $message;
        $this->user = $user;
        $this->department = $department;
        
    }
    
    /**
     * 消息列表
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View|string
     */
    public function index() {
        #获取用户信息
        $corpId = 'wxe75227cead6b8aec';
        $secret = 'qv_kkW2S3zmMWIUrV3u2nydcyIoLknTvuDMq7ja4TYE';
        $agentId = 3;
        $userId = Session::get('userId') ? Session::get('userId') : null;
        $code = Request::input('code');
        if (empty($code) && empty($userId)) {
            $codeUrl = Wechat::getCodeUrl($corpId, $agentId, 'http://weixin.028lk.com/message_center');
            return redirect($codeUrl);
        }elseif(!empty($code) && empty($userId)){
            $accessToken = Wechat::getAccessToken($corpId, $secret);
            $userInfo = json_decode(Wechat::getUserInfo($accessToken, $code), JSON_UNESCAPED_UNICODE);
            $userId = $userInfo['UserId'];
            Session::put('userId',$userId);
        }
        print_r($userId);
        die;
        // $userId = 'yuanhongbin';
        // Session::put('userId',$userId);
        $user = User::whereUserid($userId)->first();
        if (Request::isMethod('post')) {
            $keywords = Request::get('keywords');
            $type = Request::get('type');
            if (!empty($keywords)) {
                switch ($type) {
                    case 'send':
                        // $sendMessages = [];
                        $sendMessages = Message::whereSUserId($user->id)
                            ->where('content', 'like', '%' . $keywords . '%')
                            ->orWhere('title', 'like', '%' . $keywords . '%')
                            ->get();
                        if (sizeof($sendMessages) != 0) {
                            foreach ($sendMessages as $s) {
                                $s['r_user_id'] = User::find($s['r_user_id'])->realname;
                            }
                        }

                        return response(['sendMessages' => $sendMessages, 'type' => $type]);
                        break;
                    case 'receive':
                        // $receiveMessages = [];
                        $receiveMessages = Message::whereRUserId($user->id)
                            ->where('content', 'like', '%' . $keywords . '%')
                            ->orWhere('title', 'like', '%' . $keywords . '%')
                            ->get();
                        if (sizeof($receiveMessages) != 0) {
                            foreach ($receiveMessages as $r) {
                                $r['s_user_id'] = User::find($r['s_user_id'])->realname;
                            }
                        }
                        
                        return response(['type' => $type, 'receiveMessages' => $receiveMessages]);
                        break;
                    default:
                        break;
                }
                
            }
            
        }
        #判断是否为教职工
        $educator = false;
        if(empty($user)){
            return '<h4>你暂不是教职员工或监护人</h4>';
        }
        if ($user->group->name != '教职员工' && $user->group->name != '监护人') {
            return '<h4>你暂不是教职员工或监护人</h4>';
        }
        if ($user->group->name == '教职员工') {
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
     * 发送消息页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {

        $userId = Session::get('userId');
        if(Request::isMethod('post')){
            $keywords = Request::get('keywords');
            if (empty($keywords)){
                $user = $this->user->where('userid', $userId)->first();
                $educator = Educator::where('user_id',$user->id)->first();
                $school = $educator->school;
                $departmentId = Department::where('name',$school->name)->first()->id;
                $departments = Department::where('parent_id', $departmentId)->get();
                $department = Department::whereId($departmentId)->first();
                $users = $department->users;
                return response()->json([
                    'department' => $department,
                    'departments'=>$departments,
                    'user'=> $users
                ]);
            }
            $users = User::where('realname', 'like', '%' . $keywords . '%')->get();
            if($users){
                return response()->json(['statusCode'=> 200, 'user'=> $users]);
            }
        }
    
        // $departmentId = 4;
        #教师可发送消息
        #取的和教师关联的学校的部门id
        $user = $this->user->where('userid', $userId)->first();
        $educator = Educator::whereUserId($user->id)->first();
        $school = $educator->school;
        $departmentId = Department::whereName($school->name)->first()->id;
        $departments = Department::whereParentId($departmentId)->get();
        $department = Department::find($departmentId);
        $users = $department->users;

        return view('wechat.message_center.create', [
            'department' => $department,
            'departments' => $departments,
            'users' => $users
        ]);
    }
    
    /**
     * 消息发送操作
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     * @throws \Throwable
     */
    public function store() {
        return $this->frontStore() ? $this->succeed() : $this->fail();
    }
    
    /**
     * 消息编辑页面
     *
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
     * 更新已读状态
     *
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
     * 消息详情页面展示
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show($id) {
        $userId = Session::get('userId');
//      $userId = "yuanhongbin";
        $user = $this->user->where('userid', $userId)->first();
        $message = $this->message->find($id);
        $edit = ($user->id == $message->s_user_id ? true : false);
        
        return view('wechat.message_center.show', ['message' => $message, 'edit' => $edit, 'show' => true]);
    }
    
    /**
     * 删除指定消息
     *
     * @param $id
     * @return bool|\Illuminate\Http\JsonResponse|null
     * @throws \Exception
     */
    public function destroy($id) {
        $message = $this->message->find($id);
        if (!$message) {
            return $this->notFound();
        }
        
        //只能删除查看的记录 不能删除多媒体文件 多媒体文件路径被多个记录存入
        return $message->delete() ? $this->succeed() : $this->fail();
    }
    
    /**
     * 消息回复
     *
     */
    public function replay(){
        $userId = Session::get('userId');
        $user = $this->user->where('userid', $userId)->first();
        $input = Request::all();
        $input['user_id'] = $user->id;
        return MessageReply::store($input) ? $this->succeed() : $this->fail();
    }
    
    /**
     * 消息回复列表
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function replayList(){
        $userId = Session::get('userId');
        $user = $this->user->where('userid', $userId)->first();
        $input = Request::all();
        $message = $this->message->find($input['id']);
        $lists = MessageReply::where('msl_id', $input['msl_id'])->get();
        if($user->id == $message->s_user_id){
            foreach ($lists as $list){
                $list->name = $list->user->realname;
            }
        }else{
            $lists = MessageReply::where('msl_id', $input['msl_id'])->where('user_id', $user->id)->get();
            foreach ($lists as $list){
                $list->name = $list->user->realname;
            }
        }
        return $lists ? $this->succeed($lists) : $this->fail();
    }
    
    /**
     * 消息回复删除
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function replayDestroy($id){
        
        $messageReply = MessageReply::find($id);
        if (!$messageReply) {
            return $this->notFound();
        }
        return $messageReply->delete() ? $this->succeed() : $this->fail();
    }
    
    
    /**
     *上传图片和视频
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function upload() {
        
        $type = Request::input('type');
        if (empty($type)) {
            $data = Media::upload(Request::file('file'), '前端消息中心');
            return $data ? $this->succeed($data) : $this->fail();
        }
        if ($type == 'mpnews') {
            $type = 'image';
        }
        $file = Request::file('file');
        if (empty($file)) {
            $result['statusCode'] = 0;
            $result['message'] = '您还未选择文件！';
            
            return $result;
        } else {
            $result['data'] = [];
            $mes = Media::upload($file, ' 前端消息中心');
            if ($mes) {
                $result['statusCode'] = 1;
                $result['message'] = '上传成功！';
                $path = dirname(public_path()) . '/' . $mes['path'];
                $data = ["media" => curl_file_create($path)];
                $crop = Corp::whereName('万浪软件')->first();
                $app = App::whereAgentid('999')->first();
                $token = Wechat::getAccessToken($crop->corpid, $app->secret);
                $status = Wechat::uploadMedia($token, $type, $data);
                $message = json_decode($status);
                if ($message->errcode == 0) {
                    $mes['media_id'] = $message->media_id;
                    $result['data'] = $mes;
                } else {
                    $result['statusCode'] = 0;
                    $result['message'] = '微信服务器上传失败！';
                }
            } else {
                $result['statusCode'] = 0;
                $result['message'] = '文件上传失败！';
            }
        }
        
        return response()->json($result);
    }
    
    /**
     * 获取下一级部门列表
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function getNextDept($id) {
        
        $department = Department::find($id);
        $users = $department->users;
        $nextDepts = Department::whereParentId($id)->get();
        $data = view('wechat.message_center.select', ['departments' => $nextDepts, 'users' => $users])->render();
        
        return $data ? $this->succeed($data) : $this->fail();
        
    }
    
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
                $msl = MessageSendingLog::find($message->msl_id);
                $msl->read_count = $msl->read_count + 1;
                
                return $msl->save() ? true : false;
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
    }
    
    /**
     *
     * 服务器端数据保存 后期用队列处理
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    private function frontStore() {
        
        $user = $this->user->where('userid', Session::get('userId'))->first();
        $input = Request::all();
        $userIds = [];
        if (!isset($input['user_ids'])) {
            $input['user_ids'] = [];
        }
        if (!isset($input['department_ids'])) {
            $input['department_ids'] = [];
        }
        if ($input['content'] == '0') {
            $input['content'] = '';
        }
    
        #处理接收者 这里先处理了一层
        if (!empty($input['department_ids'])) {
            #获取该部门下包括子部门的user
            $users = $this->department->getPartyUser($input['department_ids']);
            foreach ($users as $user) {
                $userIds[] = $user->id;
            }
        }
        $receiveUserIds = array_unique(array_merge($input['user_ids'], $userIds));
        #判断是否是短信，调用接口不一样
        if ($input['type'] == 'sms') {
            try {
                DB::transaction(function () use ($receiveUserIds, $input, $user) {
                    $messageSendingLog = new MessageSendingLog();
                    #新增一条日志记录（指定批次）
                    $sendLogData = [
                        'read_count'      => count($receiveUserIds),
                        'received_count'  => count($receiveUserIds),
                        'recipient_count' => count($receiveUserIds),
                    ];
                    $input['msl_id'] = $messageSendingLog->create($sendLogData)->id;
                    if (isset($input['media_ids'])) {
                        $input['media_ids'] = implode(',', $input['media_ids']);
                    } else {
                        $input['media_ids'] = '0';
                    }
                    foreach ($receiveUserIds as $receiveUserId) {
                        $messageData = [
                            'title'           => $input['title'],
                            'comm_type_id'    => CommType::whereName('短信')->first()->id,
                            'app_id'          => App::whereName('信息发送')->first()->id,
                            'msl_id'          => $input['msl_id'],
                            'content'         => $input['content'],
                            'serviceid'       => 0,
                            'message_id'      => 0,
                            'url'             => '0',
                            'media_ids'       => $input['media_ids'],
                            's_user_id'       => $user->id,
                            'r_user_id'       => $receiveUserId,
                            'message_type_id' => MessageType::whereName('test')->first()->id,
                            'readed'          => 1,
                            'sent'            => 1,
                        ];
                        $this->message->create($messageData);
                    }
                    
                    #调用短信接口
                    return $this->frontSendSms($input);
                });
            } catch (Exception $e) {
                throw $e;
            }
        } else {
            try {
                DB::transaction(function () use ($receiveUserIds, $input, $user) {
                    $messageSendingLog = new MessageSendingLog();
                    #新增一条日志记录（指定批次）
                    $sendLogData = [
                        'read_count'      => 0,
                        'received_count'  => 0,
                        'recipient_count' => count($receiveUserIds),
                    ];
                    $input['msl_id'] = $messageSendingLog->create($sendLogData)->id;
                    $msl = $messageSendingLog->whereId($input['msl_id'])->first();
                    if (isset($input['media_ids'])) {
                        $input['media_ids'] = implode(',', $input['media_ids']);
                    } else {
                        $input['media_ids'] = '0';
                    }
                    foreach ($receiveUserIds as $receiveUserId) {
                        $messageData = [
                            'title'           => $input['title'],
                            'comm_type_id'    => CommType::whereName('应用')->first()->id,
                            'app_id'          => App::whereName('信息发送')->first()->id,
                            'msl_id'          => $input['msl_id'],
                            'content'         => $input['content'],
                            'serviceid'       => 0,
                            'message_id'      => 0,
                            'url'             => '0',
                            'media_ids'       => $input['media_ids'],
                            's_user_id'       => $user->id,
                            'r_user_id'       => $receiveUserId,
                            'message_type_id' => MessageType::whereName('test')->first()->id,
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
                    $message = $this->message->where('msl_id', $input['msl_id'])->first();
                    $url = 'weixin.028lk.com/message_show/' . $message->id;
                    
                    return $this->frontSendMessage($input, $url);
                });
            } catch (Exception $e) {
                throw $e;
            }
        }
        
        return true;
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
        $token = Wechat::getAccessToken($corpId, $secret, $url);
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
        
        return $status->errcode == 0 ? true : false;
    }
    
    /**
     * 短信消息发送
     *
     * @param $input
     * @return bool
     */
    private function frontSendSms($input) {
        #调用短信接口
        $code = $this->message->sendSms($input['user_ids'], $input['department_ids'], $input['content']);
        
        return $code > 0 ? true : false;
    }
}

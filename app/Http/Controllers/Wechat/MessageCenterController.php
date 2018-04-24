<?php
namespace App\Http\Controllers\Wechat;

use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\WechatTrait;
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\CommType;
use App\Models\Corp;
use App\Models\Department;
use App\Models\DepartmentUser;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\Media;
use App\Models\Message;
use App\Models\MessageReply;
use App\Models\MessageSendingLog;
use App\Models\MessageType;
use App\Models\Squad;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * @return Factory|RedirectResponse|Redirector|View|string
     */
    public function index() {
        
        #获取用户信息
        if (!Auth::id()) {
            return $this->getUserid(self::APP);
        } else {
            list($received, $sent, $count, $educator) = $this->message->wIndex();
    
            return view('wechat.message_center.index', [
                'receiveMessages' => $received,
                'sendMessages'    => $sent,
                'count'           => $count,
                'educator'        => $educator,
            ]);
        }
    }
    
    /**
     * 发送消息页面
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create() {
        
        $userId = Auth::id();
        if (Request::isMethod('post')) {
            $keywords = Request::get('keywords');
            if (empty($keywords)) {
                $lists = $this->initLists($userId);
                
                return response()->json([
                    'department' => $lists['department'],
                    'graLists'   => $lists['graLists'],
                    'claLists'   => $lists['claLists'],
                    'users'      => $lists['users'],
                ]);
            }
            if (!in_array(Auth::user()->group->name, Constant::SUPER_ROLES)) {
                $studentIds = $this->du->contactIds('student');
                $students = Student::whereIn('id', $studentIds)->get();
                $userIds[] = User::whereUserid($userId)->first()->id;
                foreach ($students as $s) {
                    $userIds[] = $s->user->id;
                }
                $users = User::whereIn('id', $userIds)
                    ->where('realname', 'like', '%' . $keywords . '%')->get();
                if ($users) {
                    return response()->json(['statusCode' => HttpStatusCode::OK, 'user' => $users]);
                }
            }
            
        }
        #教师可发送消息
        #取的和教师关联的学校的部门id
        $lists = $this->initLists($userId);
        
        return view('wechat.message_center.create', [
            'department' => $lists['department'],
            'graLists'   => $lists['graLists'],
            'claLists'   => $lists['claLists'],
            'users'      => $lists['users'],
        ]);
    }
    
    /**
     * 初始化发送对象列表
     * @param $userId
     * @return array
     */
    private function initLists($userId) {
        
        $user = User::where('userid', $userId)->first();
        $educator = Educator::where('user_id', $user->id)->first();
        $school = $educator->school;
        $departmentId = Department::where('name', $school->name)->first()->id;
        $department = Department::whereId($departmentId)->first();
        #找出教师关联的班级
        $classes = $educator->classes;
        #找出教师关联的年级 且判断是否为年级主任
        $gradeId = [];
        $squadId = [];
        $classId = [];
        #年级主任对应的年级
        $grades = Grade::whereEnabled(1)->where('school_id', $school->id)->get();
        foreach ($grades as $gra) {
            if (in_array($educator->id, explode(',', $gra->educator_ids))) {
                $gradeId[] = $gra->department_id;
            }
        }
        #班级主任对应的班级id, 需要排除年级下的班级
        $squads = Squad::whereEnabled(1)->get();
        foreach ($squads as $sq) {
            $grade = $sq->grade;
            if (!in_array($educator->id, explode(',', $grade->educator_ids))
                && in_array($educator->id, explode(',', $sq->educator_ids))) {
                $squadId[] = $sq->department_id;
            }
        }
        #找出科任老师对应的班级ids 排除属于班主任对应的班级和属于年级主任对应的班级
        foreach ($classes as $squad) {
            $grade = $squad->grade;
            if (!in_array($educator->id, explode(',', $grade->educator_ids))
                && !in_array($educator->id, explode(',', $squad->educator_ids))) {
                $classId[] = $squad->department_id;
            }
        }
        $data = [
            #年级列表
            'graLists'   => Department::whereIn('id', $gradeId)->get(),
            #班级列表
            'claLists'   => Department::whereIn('id', array_merge($squadId, $classId))->get(),
            #初始人员列表
            'users'      => false,
            #学校对应的部门
            'department' => $department,
        ];
        
        return $data;
    }
    
    /**
     * 消息发送操作
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws Exception
     * @throws \Throwable
     */
    public function store() {
        
        return $this->result(
            $this->frontStore()
        );
        
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
                            'app_id'          => App::whereName('消息中心')->first()->id,
                            'msl_id'          => $input['msl_id'],
                            'content'         => $input['content'],
                            'serviceid'       => 0,
                            'message_id'      => 0,
                            'url'             => '0',
                            'media_ids'       => $input['media_ids'],
                            's_user_id'       => $user->id,
                            'r_user_id'       => $receiveUserId,
                            'message_type_id' => MessageType::whereName('消息通知')->first()->id,
                            'read'            => 1,
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
                            'app_id'          => App::whereName('消息中心')->first()->id,
                            'msl_id'          => $input['msl_id'],
                            'content'         => $input['content'],
                            'serviceid'       => 0,
                            'message_id'      => 0,
                            'url'             => '0',
                            'media_ids'       => $input['media_ids'],
                            's_user_id'       => $user->id,
                            'r_user_id'       => $receiveUserId,
                            'message_type_id' => MessageType::whereName('消息通知')->first()->id,
                            'read'            => 0,
                            'sent'            => 0,
                        ];
                        $message = $this->message->create($messageData);
                        //这里的判断是无效的，应该放在应用发送后返回正确的状态值后
                        $message->sent = 1;
                        $message->save();
                        #更新msl表
                        $msl->received_count = $msl->received_count + 1;
                        $msl->save();
                    }
                    #推送微信服务器且显示详情页
                    $msg = $this->message->where('msl_id', $input['msl_id'])->first();
                    $url = 'http://weixin.028lk.com/message_show/' . $msg->id;
                    
                    return $this->frontSendMessage($input, $url);
                });
            } catch (Exception $e) {
                throw $e;
            }
        }
        
        return true;
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
        
        return $code > 0;
        
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     * @throws \Throwable
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
     * @throws \Throwable
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
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
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
     * @return bool|\Illuminate\Http\JsonResponse|null
     * @throws \Exception
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
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
     * @return \Illuminate\Http\JsonResponse
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
    
    /**
     * 获取下一级部门列表
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Throwable
     */
    public function department($id) {
        
        #判断传过来的id是否为学校id
        $department = Department::whereId($id)->first();
        if ($department->department_type_id == 4) {
            $lists = $this->initLists(Auth::id());
            $data = view('wechat.message_center.select', [
                'graLists' => $lists['graLists'],
                'claLists' => $lists['claLists'],
                'users'    => $lists['users'],
            ])->render();
            
        } else {
            $users = $department->users;
            $nextDepts = Department::where('parent_id', $id)->get();
            $data = view('wechat.message_center.select', [
                'departments' => $nextDepts,
                'users'       => $users,
            ])->render();
        }
        
        return $this->result($data, $data);
        
    }
    
}

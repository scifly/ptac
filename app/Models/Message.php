<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\MessageRequest;
use App\Jobs\SendMessage;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * App\Models\Message
 *
 * @property int $id
 * @property int $comm_type_id 通信方式id
 * @property int $app_id 应用id
 * @property int $msl_id 消息发送批次id
 * @property string $title 消息标题
 * @property string $content 消息内容
 * @property string $serviceid 业务id
 * @property int $message_id 关联的消息ID
 * @property string $url HTML页面地址
 * @property string $media_ids 多媒体IDs
 * @property int $s_user_id 发送者用户ID
 * @property int $r_user_id 接收者用户IDs
 * @property int $message_type_id 消息类型ID
 * @property int $read 是否已读
 * @property int $sent 消息发送是否成功
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read CommType $commType
 * @property-read MessageSendingLog $messageSendinglogs
 * @property-read MessageType $messageType
 * @property-read User $receiveUser
 * @property-read User $user
 * @method static Builder|Message whereAppId($value)
 * @method static Builder|Message whereCommTypeId($value)
 * @method static Builder|Message whereContent($value)
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereMediaIds($value)
 * @method static Builder|Message whereMessageId($value)
 * @method static Builder|Message whereMessageTypeId($value)
 * @method static Builder|Message whereMslId($value)
 * @method static Builder|Message whereRUserId($value)
 * @method static Builder|Message whereRead($value)
 * @method static Builder|Message whereSUserId($value)
 * @method static Builder|Message whereSent($value)
 * @method static Builder|Message whereServiceid($value)
 * @method static Builder|Message whereTitle($value)
 * @method static Builder|Message whereUpdatedAt($value)
 * @method static Builder|Message whereUrl($value)
 * @mixin Eloquent
 */
class Message extends Model {
    
    // todo: needs to be optimized
    use ModelTrait;
    
    protected $table = 'messages';
    
    protected $fillable = [
        'comm_type_id', 'app_id', 'msl_id', 'content',
        'serviceid', 'message_id', 'url', 'media_ids',
        's_user_id', 'r_user_id', 'message_type_id',
        'read', 'sent', 'title',
    ];
    
    /**
     * 返回指定消息所属的消息类型对象
     *
     * @return BelongsTo
     */
    function messageType() { return $this->belongsTo('App\Models\MessageType'); }
    
    /**
     * 返回指定消息所属的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User', 's_user_id', 'id'); }
    
    /**
     * 返回指定消息接收的用户对象
     *
     * @return BelongsTo
     */
    function receiveUser() { return $this->belongsTo('App\Models\User', 'r_user_id', 'id'); }
    
    /**
     * 获取
     *
     * @param array $classIds
     * @return Collection|static[]
     */
    function classes(array $classIds) { return Squad::whereIn('id', $classIds)->get(['id', 'name']); }
    
    /**
     * 返回对应的消息发送日志对象
     *
     * @return BelongsTo
     */
    function messageSendinglog() { return $this->belongsTo('App\Models\MessageSendingLog', 'msl_id', 'id'); }
    
    /**
     * 返回对应的通信类型对象
     *
     * @return BelongsTo
     */
    function commType() { return $this->belongsTo('App\Models\CommType'); }
    
    /**
     * 创建并发送消息
     *
     * @param MessageRequest $request
     * @return bool
     * @throws Exception
     * @throws Throwable
     */
    function store(MessageRequest $request) {
        
        list($users, $mobiles) = $this->targets(
            $request['user_ids'], $request['department_ids']
        );
        #判断是否是短信，调用接口不一样
        $msl = MessageSendingLog::create([
            'read_count'      => count($users),
            'received_count'  => count($users),
            'recipient_count' => count($users),
        ]);
        if ($request['type'] == 'sms') {
            try {
                DB::transaction(function () use ($request, $msl, $users, $mobiles) {
                    $request['content']['media_ids'] = 0;
                    $result = $this->sendSms($mobiles, $request['content']);
                    $sent = $read = count($users);
                    if ($result > 0) {
                        $sent = $read = 0;
                        $msl->update([
                            'received_count'  => $sent,
                            'recipient_count' => $read,
                        ]);
                    }
                    $this->log($users, $msl->id, 'n/a', $request['content'], $sent, $read);
                });
            } catch (Exception $e) {
                throw $e;
            }
        } else {
            try {
                DB::transaction(function () use ($request, $msl, $users) {
                    $request['content']['media_ids'] = isset($request['media_ids'])
                        ? implode(',', $request['media_ids']) : 0;
                    /** @var User $user */
                    foreach ($users as $user) {
                        $messageData = [
                            'title'           => $request['title'],
                            'comm_type_id'    => CommType::whereName('应用')->first()->id,
                            'app_id'          => App::whereName('消息中心')->first()->id,
                            'msl_id'          => $request['msl_id'],
                            'content'         => $request['content'],
                            'serviceid'       => 0,
                            'message_id'      => 0,
                            'url'             => '0',
                            'media_ids'       => $request['media_ids'],
                            's_user_id'       => Auth::id(),
                            'r_user_id'       => $user->id,
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
                    $msg = $this->message->where('msl_id', $request['msl_id'])->first();
                    $url = 'http://weixin.028lk.com/message_show/' . $msg->id;
                    
                    return $this->frontSendMessage($request, $url);
                });
            } catch (Exception $e) {
                throw $e;
            }
        }
        
        return true;
        
    }
    
    /**
     * @param MessageRequest $request
     * @param $id
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    function modify(MessageRequest $request, $id) {
        
        $message = self::find($id);
        if (!$message) {
            return false;
        }
        try {
            DB::transaction(function () use ($request, $id) {
                self::removeMedias($request);
                
                return self::find($id)->update($request->except('_method', '_token'));
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    /**
     * 消息列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'Message.id', 'dt' => 0],
            ['db' => 'CommType.name as commtypename', 'dt' => 1],
            ['db' => 'App.name as appname', 'dt' => 2],
            ['db' => 'Message.msl_id', 'dt' => 3],
            ['db' => 'User.realname', 'dt' => 4],
            ['db' => 'MessageType.name as messagetypename', 'dt' => 5],
            ['db' => 'Message.created_at', 'dt' => 6],
            [
                'db'        => 'Message.sent', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    $sent = Snippet::status($d, '已发', '未发');
                    $read = Snippet::status($row['read'], '已读', '未读');
                    
                    return $sent . '&nbsp;' . $read;
                },
            ],
            ['db' => 'Message.read', 'dt' => 8],
        ];
        $joins = [
            [
                'table'      => 'comm_types',
                'alias'      => 'CommType',
                'type'       => 'INNER',
                'conditions' => [
                    'CommType.id = Message.comm_type_id',
                ],
            ],
            [
                'table'      => 'apps',
                'alias'      => 'App',
                'type'       => 'INNER',
                'conditions' => [
                    'App.id = Message.app_id',
                ],
            ],
            [
                'table'      => 'message_types',
                'alias'      => 'MessageType',
                'type'       => 'INNER',
                'conditions' => [
                    'MessageType.id = Message.message_type_id',
                ],
            ],
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Message.r_user_id',
                ],
            ],
        ];
        $condition = null;
        $role = Auth::user()->group->name;
        if (in_array($role, Constant::SUPER_ROLES)) {
            $userIds[] = Auth::id();
            $userIds = array_unique(
                array_merge(
                    $this->userIds(School::find($this->schoolId())->department_id),
                    $userIds
                )
            );
            $condition = 'Message.s_user_id IN' . '(' . implode(',', $userIds) . ')';
        } else {
            $condition = 'Message.s_user_id = ' . Auth::id()
                . ' OR Message.r_user_id = ' . Auth::id();
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 发送消息
     *
     * @param $data
     * @return bool
     */
    function send($data) {
        
        abort_if(
            empty($data['user_ids']) && empty($data['dept_ids']) && empty($data['app_ids']),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.message.empty_targets')
        );
        $apps = App::whereIn('id', $data['app_ids'])->get()->toArray();
        $corp = School::find($this->schoolId())->corp;
        abort_if(!$corp, HttpStatusCode::NOT_FOUND, __('messages.message.invalid_corp'));
        SendMessage::dispatch($data, Auth::id(), $corp, $apps);
        
        return true;
        
    }
    
    /**
     * 发送短信
     *
     * @param $mobiles
     * @param $content
     * @return string
     */
    function sendSms($mobiles, $content) {
        
        $signature = '【成都外国语】';
        $result = Wechat::batchSend(
            'LKJK004923', "654321@",
            implode(',', $mobiles),
            $content . $signature
        );
        
        return json_encode($result);
        
    }
    
    /**
     * 记录消息发送日志
     *
     * @param $users - 发送对象
     * @param $sUserId
     * @param $mslId - 发送日志id
     * @param $title - 消息抬头
     * @param $content - 消息内容（文本）
     * @param $sent - 是否已发送
     * @param $read - 是否已读
     * @param $msgTypeId - 消息类型
     * @param null $appId - 应用id
     */
    function log($users, $sUserId, $mslId, $title, $content, $sent, $read, $msgTypeId, $appId = null) {
    
        $commType = !$appId ? '短信' : '微信';
        $failedUserIds = [];
        if ($commType === '微信') {
            $failedUserIds = $this->failedUserIds($sent['invaliduser'], $sent['invalidparty']);
        }
        foreach ($users as $user) {
            if ($commType === '微信') {
                $sent = !in_array($user->id, $failedUserIds);
            }
            $this->create([
                'comm_type_id'    => CommType::whereName($commType)->first()->id,
                'app_id'          => $appId ?? 0,
                'msl_id'          => $mslId,
                'title'           => $title,
                'content'         => json_encode($content),
                'serviceid'       => 0,
                'message_id'      => 0,
                'url'             => '',
                'media_ids'       => '0',
                's_user_id'       => $sUserId,
                'r_user_id'       => $user->id,
                'message_type_id' => $msgTypeId,
                'sent'            => $sent ? 1 : 0,
                'read'            => $read ? 1 : 0,
            ]);
        }
        
    }
    
    /**
     * 根据用户id和部门id获取消息发送对象（手机号码，用户对象）
     *
     * @param $userIds
     * @param $deptIds
     * @return array
     */
    function targets($userIds = [], $deptIds = []) {
        
        $userIds = array_unique(
            array_merge(
                DepartmentUser::whereIn('department_id', $deptIds)->pluck('user_id')->toArray(),
                $userIds
            )
        );
        $users = User::whereIn('id', $userIds)->get();
        $mobiles = Mobile::whereIn('user_id', $userIds)->where('enabled', 1)->pluck('mobile')->toArray();
        
        return [$users, $mobiles];
        
    }
    
    /**
     * 获取发送失败的接收者用户id
     *
     * @param $userids
     * @param $deptIds
     * @return mixed
     */
    function failedUserIds($userids, $deptIds) {
    
        $userIds = User::whereIn('userid', explode('|', $userids))->pluck('id')->toArray();
        $deptIds = explode('|', $deptIds);
        list($failedUsers) = $this->targets($userIds, $deptIds);
        
        return $failedUsers->pluck('id')->toArray();
        
    }
    
    /**
     * 上传媒体文件
     *
     * @return JsonResponse
     */
    function upload() {
        
        # 上传到本地后台
        $media = new Media();
        $file = Request::file('file');
        $type = Request::input('type');
        abort_if(
            empty($file),
            HttpStatusCode::NOT_ACCEPTABLE,
            '您还未选择文件！'
        );
        $uploadedFile = $media->upload($file, '消息中心');
        abort_if(
            !$uploadedFile,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            '文件上传失败'
        );
        # 上传到企业号后台
        list($corpid, $secret) = $this->tokenParams();
        $token = Wechat::getAccessToken($corpid, $secret);
        if ($token['errcode']) {
            abort(
                HttpStatusCode::INTERNAL_SERVER_ERROR,
                $token['errmsg']
            );
        }
        $contentType = '';
        switch ($type) {
            case 'image': $contentType = 'image/*'; break;
            case 'voice': $contentType = 'audio/*'; break;
            case 'video': $contentType = 'video/*'; break;
            case 'file': $contentType = '*'; break;
            default: break;
        }
        $result = json_decode(
            Wechat::uploadMedia(
                $token['access_token'],
                Request::input('type'),
                [
                    'file-contents' => curl_file_create(public_path($uploadedFile['path'])),
                    'filename' => $uploadedFile['filename'],
                    'content-type' => $contentType,
                    'filelength' => $file->getSize(),
                ]
            )
        );
        abort_if(
            $result->{'errcode'},
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            Wechat::ERRMSGS[$result->{'errcode'}]
        );
        $uploadedFile['media_id'] = $result->{'media_id'};
        
        return response()->json([
            'message' => __('messages.message.uploaded'),
            'data' => $uploadedFile
        ]);
        
    }
    
    /**
     * 微信端消息中心首页
     *
     * @return array
     * @throws Throwable
     */
    function wIndex() {
        
        if (Request::method() == 'POST') {
            return $this->search();
        }
        $user = Auth::user();
        $schoolIds = $user->schoolIds($user->id, session('corpId'));
        if (Request::query(''))
        if (count($schoolIds) > 1)
        
        return view('wechat.message_center.index');
        
    }
    
    /**
     * 搜索（消息或发送对象）
     *
     * @param null $departmentId
     * @return array
     * @throws Throwable
     */
    function search($departmentId = null) {
        
        if (!isset($departmentId)) {
            if (Request::has('type')) {
                $response = $this->searchMessage();
            } else {
                $response = $this->searchTarget();
            }
        } else {
            $response = $this->subTargets($departmentId);
        }

        return $response;
        
    }
    
    /**
     * 搜索已发或收到的消息
     *
     * @return array
     */
    private function searchMessage() {
    
        # 搜索已发或收到的消息
        $user = Auth::user();
        $keyword = Request::input('keyword');
        $type = Request::input('type');
        $response = ['type' => $type];
        switch ($type) {
            case 'sent':
                $sent = Message::whereSUserId($user->id)
                    ->where('content', 'like', '%' . $keyword . '%')
                    ->orWhere('title', 'like', '%' . $keyword . '%')
                    ->get();
                if (sizeof($sent) != 0) {
                    foreach ($sent as $s) {
                        $s['user'] = User::find($s['r_user_id'])->realname;
                    }
                }
                $response['messages'] = $sent;
                break;
            case 'received':
                $received = Message::whereRUserId($user->id)
                    ->where('content', 'like', '%' . $keyword . '%')
                    ->orWhere('title', 'like', '%' . $keyword . '%')
                    ->get();
                if (sizeof($received) != 0) {
                    foreach ($received as $r) {
                        $r['user'] = User::find($r['user'])->realname;
                    }
                }
                $response['messages'] = $received;
                break;
            default:
                break;
        }
        
        return $response;
        
    }
    
    /**
     * 搜索发送对象
     *
     * @return array
     */
    private function searchTarget() {
    
        # 搜索发送对象
        $user = Auth::user();
        $response = [];
        $keyword = Request::input('keyword');
        if (empty($keyword)) {
            $response = [
                'gradeDepts' => (new Grade())->departments($user->id),
                'classDepts' => (new Squad())->departments($user->id),
                'schoolDept' => Department::find($user->educator->school->department_id),
                'users'      => [],
            ];
        }
        if (!in_array($user->group->name, Constant::SUPER_ROLES)) {
            $keyword = '%' . $keyword . '%';
            $studentIds = $this->contactIds('student', $user, $user->educator->school_id);
            $userIds = Student::whereIn('id', $studentIds)->get()->pluck('user_id')->toArray();
            $users = User::whereIn('id', $userIds)->where('realname', 'like', $keyword)->get();
            $response = [
                'users' => $users,
            ];
        }
        
        return $response;
        
    }
    
    /**
     * 搜索指定部门包含的发送对象（部门或用户）
     *
     * @param $departmentId
     * @return string
     * @throws Throwable
     */
    private function subTargets($departmentId) {
    
        $user = Auth::user();
        $department = Department::find($departmentId);
        if ($department->department_type->name == '学校') {
            $response = view('wechat.message_center.select', [
                'gradeDepts' => (new Grade())->departments($user->id),
                'classDepts' => (new Squad())->departments($user->id),
                'users'    => Collect([]),
            ])->render();
        } else {
            $users = $department->users;
            $nextDepts = Department::where('parent_id', $departmentId)->get();
            $response = view('wechat.message_center.select', [
                'departments' => $nextDepts,
                'users'       => $users,
            ])->render();
        }
        
        return $response;
        
    }
    
    /**
     * 移除媒体对象
     *
     * @param $request
     * @throws Exception
     */
    private function removeMedias(MessageRequest $request) {
        
        //删除原有的图片
        $mediaIds = $request->input('del_ids');
        if ($mediaIds) {
            $medias = Media::whereIn('id', $mediaIds)->get(['id', 'path']);
            foreach ($medias as $media) {
                $paths = explode("/", $media->path);
                Storage::disk('uploads')->delete($paths[5]);
            }
            try {
                Media::whereIn('id', $mediaIds)->delete();
            } catch (Exception $e) {
                throw $e;
            }
        }
    }
    
    
}


<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Facades\Wechat;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
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
 * @property-read \App\Models\MessageSendingLog $messageSendinglog
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
     * 消息列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Message.id', 'dt' => 0],
            [
                'db' => 'CommType.name as commtypename', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    $content = json_decode($row['content']);
                    if (!isset($content->{'msgtype'})) {
                        $content = json_decode($content);
                        if (!isset($content->{'msgtype'})) {
                            return $d;
                        }
                    }
                    $type = '(' . Constant::INFO_TYPES[$content->{'msgtype'}] .
                        ($row['sent'] ? '' : sprintf(Snippet::BADGE_GRAY, ' . 草稿')) . ')';
                    return $d . sprintf(Snippet::BADGE_GREEN, $type);
                }
            ],
            [
                'db' => 'App.name as appname', 'dt' => 2,
                'formatter' => function ($d) {
                    return $d ?? sprintf(Snippet::BADGE_GRAY, '(n/a)');
                }
            ],
            [
                'db' => 'Message.msl_id', 'dt' => 3,
                'formatter' => function ($d) {
                    return $d ? $d : sprintf(Snippet::BADGE_GRAY, '(n/a)');
                }
            ],
            [
                'db' => 'User.realname', 'dt' => 4,
                'formatter' => function ($d) {
                    return $d ?? sprintf(Snippet::BADGE_GRAY, '(部门 & 会员)');
                }
            ],
            ['db' => 'MessageType.name as messagetypename', 'dt' => 5],
            [
                'db' => 'Message.created_at', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return $row['sent'] ? $d : sprintf(Snippet::BADGE_GRAY, '(n/a)');
                }
            ],
            [
                'db'        => 'Message.sent', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    $id = $row['id'];
                    $sent = Snippet::status($d, '已发', '未发');
                    $read = Snippet::status($row['read'], '已读', '未读');
                    $editHtml = '<a id="%s" title="编辑" href="#"><i class="fa fa-edit" style="margin-left: 15px;"></i></a>';
                    $showHtml = '<a id="%s" title="详情" href="#"><i class="fa fa-laptop" style="margin-left: 15px;"></i></a>';
                    $status = $sent . $read;
                    $status .= !$d
                        ? sprintf($editHtml, 'edit_' . $id)
                        : sprintf($showHtml, 'show_' . $id);
                    
                    return $status . sprintf(Snippet::DT_LINK_DEL, $id);
                },
            ],
            ['db' => 'Message.read', 'dt' => 8],
            ['db' => 'Message.content', 'dt' => 9],
            ['db' => 'Message.app_id', 'dt' => 10],
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
                'type'       => 'LEFT',
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
                'type'       => 'LEFT',
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
            $condition = 'Message.s_user_id IN' . ' (' . implode(',', $userIds) . ')';
        } else {
            $condition = 'Message.s_user_id = ' . Auth::id()
                . ' OR Message.r_user_id = ' . Auth::id();
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 编辑消息
     *
     * @param $id
     * @return array
     */
    function edit($id) {
    
        list($content) = $this->show($id);
        $message = $content[$content['type']];
        $toparty = $message->{'toparty'};
        $touser = $message->{'touser'};
        $targetIds = !empty($toparty) ? explode('|', $toparty) : [];
        $userids = !empty($touser) ? explode('|', $touser) : [];
        $users = User::whereIn('userid', $userids)->get();
        foreach ($users as $user) {
            foreach ($user->departments as $department) {
                $targetIds[] = 'user-' . $department->id . '-' . $user->id;
            }
        }
        $targetsHtml = '';
        foreach ($targetIds as $targetId) {
            $paths = explode('-', $targetId);
            if (sizeof($paths) > 1) {
                $user = User::find($paths[2]);
                $targetsHtml .= sprintf(
                    Snippet::TREE_NODE,
                    'fa fa-user',
                    $user->realname,
                    $paths[2]
                );
            } else {
                $department = Department::find($targetId);
                $targetsHtml .= sprintf(
                    Snippet::TREE_NODE,
                    Constant::NODE_TYPES[$department->departmentType->name]['icon'],
                    $department->name,
                    $targetId
                );
            }
        }
    
        return [
            'selectedTargetIds' => $targetIds,
            'targets' => $targetsHtml,
            'messageTypeId' => $this->find($id)->message_type_id,
            'messageFormat' => $content['type'],
            'message' => $message
        ];
        
    }
    
    /**
     * 保存消息（草稿）
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
    
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新消息（草稿）或 批量标记已读/未读
     *
     * @param array $data
     * @param null $id
     * @return bool
     * @throws Exception
     */
    function modify(array $data, $id = null) {
    
        if (!$id) {
            try {
                DB::transaction(function () {
                    $ids = Request::input('ids');
                    $read = Request::input('action') == 'enable' ? true : false;
                    foreach ($ids as $id) {
                        $this->read($id, $read);
                    }
                });
            } catch (Exception $e) {
                throw $e;
            }
            return true;
        }
    
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 将指定消息的状态更新为已读，并更新指定消息的已读数量
     *
     * @param $id
     * @param bool $read - 1:已读，0:未读
     * @return bool
     * @throws Exception
     */
    function read($id, $read = true) {
        
        $message = $this->find($id);
        abort_if(
            !$message,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        try {
            DB::transaction(function () use ($message, $id, $read) {
                $message->read = $read ? 1 : 0;
                $message->save();
                $msl = MessageSendingLog::find($message->msl_id);
                $msl->read_count += $read ? 1 : -1;
                $msl->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 显示指定消息的内容
     *
     * @param $id
     * @return array
     */
    function show($id) {
        
        $user = Auth::user();
        $message = $this->find($id);
        $edit = ($user->id == $message->s_user_id ? true : false);
        $object = json_decode($message->content);
        $title = $message->title;
        $type = array_search(mb_substr($message->title, -3, 2), Constant::INFO_TYPES);
        if (!$type) {
            $messageType = MessageType::find($message->message_type_id);
            $messageTypeName = $messageType ? $messageType->name : '未知消息';
            if (is_object($object) && property_exists(get_class($object), 'msgtype')) {
                $type = $object->{'msgtype'};
                $title = $messageTypeName . '(' . Constant::INFO_TYPES[$type] . ')';
            } else {
                $title = $messageTypeName . '(未知)';
            }
            $message->update(['title' => $title]);
        }
        $type = $type ? $type : 'other';
        Carbon::setLocale('zh');
        $msl = $message->messageSendinglog;
        $content = [
            'id'         => $message->id,
            'title'      => $title,
            'updated_at' => Carbon::createFromFormat('Y-m-d H:i:s', $message->updated_at)->diffForHumans(),
            'sender'     => User::find($message->s_user_id)->realname,
            'recipients' => $msl ? $msl->recipient_count : 0,
            'msl_id'     => $msl ? $msl->id : 0,
            'type'       => $type,
            $type        => $type == 'other' ? $message->content : $object,
        ];
        
        return [$content, $edit];
        
    }
    
    /**
     * 返回消息详情
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws Throwable
     */
    function detail($id) {
    
        list($content) = $this->show($id);
        $type = $content['type'];
        $userids = explode('|', $content[$type]->{'touser'});
        $deptIds = explode('|', $content[$type]->{'toparty'});
        $recipients = array_merge(
            User::whereIn('userid', $userids)->pluck('realname')->toArray(),
            Department::whereIn('id', $deptIds)->pluck('name')->toArray()
        );
        $msgBody = '';
        $message = $content[$type]->{$type};
        switch ($type) {
            case 'text':
                $msgBody = $message->{'content'};
                break;
            case 'image':
                $msgBody = '<img src="' . $message->{'path'} . '" alt="" style="height: 200px;" />';
                break;
            case 'voice':
                $msgBody = '<a href="' . $message->{'path'} . '">下载语音</a>';
                break;
            case 'video':
                $msgBody = '<dl>' .
                    '<dt>' . '标题: ' . '</dt>' .
                    '<dd>' . $message->{'title'} . '</dd>' .
                    '<dt>' . '描述: ' . '</dt>' .
                    '<dd>' . $message->{'description'} . '</dd>' .
                    '<dt>视频: </dt>' .
                    '<dd>' .
                        '<video height="200" controls>' .
                            '<source src="' . $message->{'path'} . '" type="video/mp4">' .
                        '</video>' .
                    '</dd>' .
                '</dl>';
                break;
            case 'file':
                $msgBody = '<a href="' . $message->{'path'} . '">下载文件</a>';
                break;
            case 'textcard':
                $msgBody = '';
                break;
            case 'mpnews':
                $msgBody = '';
                break;
            case 'sms':
                $msgBody = $message;
                break;
            default:
                break;
        }
        $m = $this->find($id);
        $app = $m->app_id ? App::find($m->app_id)->toArray() : null;
        return view('message.detail', [
            'msgTitle' => $content['title'],
            'app' => $app,
            'msgBody' => $msgBody,
            'sentAt' => $content['updated_at'],
            'recipients' => implode('; ', $recipients),
            'sender' => $content['sender']
        ])->render();
    
    }
    
    /**
     * （批量）删除消息
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id = null) {
        
        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
        
    }
    
    /**
     * 从消息中删除指定用户
     *
     * @param $userId
     * @throws Exception
     */
    function removeUser($userId) {
        
        try {
            DB::transaction(function () use ($userId) {
                Message::whereRUserId($userId)->delete();
                Message::whereSUserId($userId)->update(['s_user_id' => 0]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
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
        $corp = School::find($this->schoolId() ?? session('schoolId'))->corp;
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
     * @throws Exception
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
     * 创建消息发送日志
     *
     * @param Collection|User[] $users - 发送对象
     * @param array $data - 消息数据
     * @return bool
     * @throws Exception
     */
    function log($users, array $data) {
        
        try {
            DB::transaction(function () use ($users, $data) {
                $commType = !$data['app_id'] ? '短信' : '微信';
                
                /** 如果发送的是微信消息，获取未发送成功的用户id */
                $failedUserIds = [];
                if ($commType === '微信') {
                    $failedUserIds = $this->failedUserIds(
                        $data['sent']['invaliduser'], $data['sent']['invalidparty']
                    );
                }
                
                /**  创建原始消息 */
                $data['sent'] = sizeof($failedUserIds) == sizeof($users) ? 0 : 1;
                $message = $this->create($data);
                
                /** 创建指定用户($users)收到的消息(应用内消息） */
                foreach ($users as $user) {
                    $data['r_user_id'] = $user->id;
                    # 设置相关消息id
                    $data['message_id'] = $message->id;
                    if ($commType === '微信') {
                        $data['sent'] = !in_array($user->id, $failedUserIds);
                    }
                    $this->create($data);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
     * 上传媒体文件
     *
     * @return JsonResponse
     * @throws Exception
     */
    function upload() {
        
        # 上传到本地后台
        $media = new Media();
        $file = Request::file('file');
        $type = Request::input('type');
        abort_if(
            empty($file),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.empty_file')
        );
        $uploadedFile = $media->upload($file, __('messages.message.title'));
        abort_if(
            !$uploadedFile,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
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
            case 'image':
                $contentType = 'image/*';
                break;
            case 'audio':
                $contentType = 'audio/*';
                $type = 'voice';
                break;
            case 'video':
                $contentType = 'video/*';
                break;
            case 'file':
                $contentType = '*';
                break;
            default:
                break;
        }
        $result = json_decode(
            Wechat::uploadMedia(
                $token['access_token'],
                $type, // Request::input('type') ,
                [
                    'file-contents' => curl_file_create(public_path($uploadedFile['path'])),
                    'filename'      => $uploadedFile['filename'],
                    'content-type'  => $contentType,
                    'filelength'    => $file->getSize(),
                ]
            )
        );
        abort_if(
            $result->{'errcode'},
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            Constant::WXERR[$result->{'errcode'}]
        );
        $uploadedFile['media_id'] = $result->{'media_id'};
        
        return response()->json([
            'message' => __('messages.message.uploaded'),
            'data'    => $uploadedFile,
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
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
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
                if (sizeof($sent)) {
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
                if (sizeof($received)) {
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
        $schoolId = $user->educator
            ? $user->educator->school_id
            : session('schoolId');
        $targets = [];
        if (Request::has('departmentId')) {
            $users = Department::find(Request::input('departmentId'))->users;
            foreach ($users as $user) {
                if ($user->custodian) {
                    $targets = [
                        'id'   => $user->id,
                        'name' => $user->realname,
                    ];
                }
            }
        } else {
            $keyword = Request::input('keyword');
            $target = Request::input('target');
            switch ($target) {
                case 'list': # 所有可见部门
                    $targets = Department::whereIn('id', $this->departmentIds($user->id, $schoolId))
                        ->get(['id', 'name'])->toArray();
                    break;
                case 'department': # 搜索指定的部门
                    $targets = Department::whereIn('id', $this->departmentIds($user->id, $schoolId))
                        ->where('name', 'like', '%' . $keyword . '%')
                        ->get(['id', 'name'])->toArray();
                    break;
                case 'user': # 搜索指定的用户
                    $deptId = Request::input('deptId');
                    $deptUserIds = Department::find($deptId)->users->pluck('id')->toArray();
                    $users = User::whereIn('id', $deptUserIds)
                        ->where('realname', 'like', '%' . $keyword . '%')
                        ->get();
                    $targets = [];
                    foreach ($users as $user) {
                        if ($user->custodian) {
                            $targets = [
                                'id'   => $user->id,
                                'name' => $user->realname,
                            ];
                        }
                    }
                    break;
                default:
                    break;
            }
        }
        
        return ['targets' => $targets];
        
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
                'gradeDepts' => (new Grade)->departments($user->id),
                'classDepts' => (new Squad)->departments($user->id),
                'users'      => Collect([]),
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
    
}


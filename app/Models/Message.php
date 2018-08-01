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
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Throwable;

/**
 * App\Models\Message 消息
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
 * @property int|null $event_id
 * @property-read CommType $commType
 * @property-read MessageType $messageType
 * @property-read User $receiver
 * @property-read User $sender
 * @property-read Event|null $event
 * @property-read MessageSendingLog $messageSendinglog
 * @method static Builder|Message whereAppId($value)
 * @method static Builder|Message whereCommTypeId($value)
 * @method static Builder|Message whereContent($value)
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereMediaIds($value)
 * @method static Builder|Message whereMessageId($value)
 * @method static Builder|Message whereMessageTypeId($value)
 * @method static Builder|Message whereMslId($value)
 * @method static Builder|Message whereEventId($value)
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
        'read', 'sent', 'title', 'event_id',
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
    function sender() { return $this->belongsTo('App\Models\User', 's_user_id', 'id'); }
    
    /**
     * 返回指定消息接收的用户对象
     *
     * @return BelongsTo
     */
    function receiver() { return $this->belongsTo('App\Models\User', 'r_user_id', 'id'); }
    
    /**
     * 返回对应的消息发送日志对象
     *
     * @return BelongsTo
     */
    function messageSendinglog() {
        
        return $this->belongsTo('App\Models\MessageSendingLog', 'msl_id', 'id');
        
    }
    
    /**
     * 返回对应的通信类型对象
     *
     * @return BelongsTo
     */
    function commType() { return $this->belongsTo('App\Models\CommType'); }
    
    /**
     * 返回对应的事件对象
     *
     * @return BelongsTo
     */
    function event() { return $this->belongsTo('App\Models\Event'); }
    
    /**
     * 消息列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Message.id', 'dt' => 0],
            [
                'db'        => 'CommType.name as commtypename', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    $content = json_decode($row['content']);
                    if (!isset($content->{'msgtype'})) {
                        $content = json_decode($content);
                        if (!isset($content->{'msgtype'})) {
                            return $d;
                        }
                    }
                    $msgType = Constant::INFO_TYPE[$content->{'msgtype'}];
                    if ($row['sent']) {
                        $type = '(' . $msgType . ')';
                    } else {
                        $type = '(' . $msgType . (!$row['event_id']
                                ? sprintf(Snippet::BADGE_RED, ' . 草稿')
                                : sprintf(Snippet::BADGE_ORANGE, ' . 定时')
                            ) . ')';
                    }

                    return $d . sprintf(Snippet::BADGE_GREEN, $type);
                },
            ],
            [
                'db'        => 'App.name as appname', 'dt' => 2,
                'formatter' => function ($d) {
                    return $d ?? sprintf(Snippet::BADGE_GRAY, '(n/a)');
                },
            ],
            [
                'db'        => 'Message.msl_id', 'dt' => 3,
                'formatter' => function ($d) {
                    return $d ? $d : sprintf(Snippet::BADGE_GRAY, '(n/a)');
                },
            ],
            [
                'db'        => 'User.realname', 'dt' => 4,
                'formatter' => function ($d) {
                    return $d ?? sprintf(Snippet::BADGE_GRAY, '(部门 & 会员)');
                },
            ],
            ['db' => 'MessageType.name as messagetypename', 'dt' => 5],
            [
                'db'        => 'Message.created_at', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return $row['sent'] ? $d : sprintf(Snippet::BADGE_GRAY, '(n/a)');
                },
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
            ['db' => 'Message.event_id', 'dt' => 11],
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
     * 保存消息（草稿）
     *
     * @param array $data
     * @param bool $draft - 是否保存为草稿
     * @return bool
     * @throws Throwable
     */
    function store(array $data, $draft = null) {
        
        try {
            DB::transaction(function () use ($data, $draft) {
                $time = null;
                if ($data['time']) {
                    $time = $data['time'];
                    unset($data['time']);
                }
                $message = $this->create($data);
                # 如果设置了发送时间，则创建消息对应的事件
                if ($time) {
                    $user = Auth::user();
                    $event = Event::create([
                        'title' => '定时消息',
                        'remark' => '定时消息',
                        'location' => 'n/a',
                        'contact' => 'n/a',
                        'url' => 'n/a',
                        'start' => $time,
                        'end' => $time,
                        'ispublic' => 0,
                        'iscourse' => 0,
                        'educator_id' => $user->educator ? $user->educator->id : 0,
                        'subject_id' => 0,
                        'alertable' => 0,
                        'alert_mins' => 0,
                        'user_id' => $user->id,
                        'enabled' => isset($draft) ? 1 : 0
                    ]);
                    $message->update(['event_id' => $event->id]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
        
    }
    
    /**
     * 编辑消息
     *
     * @param $id
     * @return array
     */
    function edit($id) {
        
        $content = $this->detail($id);
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
        $timing = $this->find($id)->event_id ? true : false;
        $time = $timing ? date('Y-m-d H:i', strtotime($this->find($id)->event->start)) : null;
        
        return [
            'selectedTargetIds' => $targetIds,
            'targets'           => $targetsHtml,
            'messageTypeId'     => $this->find($id)->message_type_id,
            'messageFormat'     => $content['type'],
            'message'           => $message,
            'timing'            => $timing,
            'time'              => $time
        ];
        
    }
    
    /**
     * 显示指定消息的内容
     *
     * @param $id
     * @return array
     */
    function detail($id) {
        
        $message = $this->find($id);
        // $edit = ($user->id == $message->s_user_id ? true : false);
        $object = json_decode($message->content);
        if (!is_object($object)) {
            $object = json_decode($object);
        }
        $title = $message->title;
        $type = array_search(mb_substr($message->title, -3, 2), Constant::INFO_TYPE);
        if (!$type) {
            $messageType = MessageType::find($message->message_type_id);
            $messageTypeName = $messageType ? $messageType->name : '未知消息';
            if (is_object($object) && property_exists(get_class($object), 'msgtype')) {
                $type = $object->{'msgtype'};
                $title = $messageTypeName . '(' . Constant::INFO_TYPE[$type] . ')';
            } else {
                $title = $messageTypeName . '(未知)';
            }
            $message->update(['title' => $title]);
        }
        $type = $type ? $type : 'other';
        $msl = $message->messageSendinglog;
        
        return [
            'id'         => $message->id,
            'title'      => $title,
            'updated_at' => $this->humanDate($message->updated_at),
            'sender'     => User::find($message->s_user_id)->realname,
            'recipients' => $msl ? $msl->recipient_count : 0,
            'msl_id'     => $msl ? $msl->id : 0,
            'type'       => $type,
            $type        => $type == 'other' ? $message->content : $object,
        ];
        
    }
    
    /**
     * 更新消息（草稿）或 批量标记已读/未读
     *
     * @param array $data
     * @param null $id
     * @return bool
     * @throws Throwable
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
        try {
            DB::transaction(function () use ($data, $id) {
                $message = $this->find($id);
                if (isset($data['time'])) {
                    # 如果设置了发送时间
                    if ($message->event_id) {
                        # 如果指定消息已有对应事件，则更新对应事件
                        Event::find($message->event_id)->update([
                            'start' => $data['time'],
                            'end' => $data['time'],
                            'enabled' => isset($data['draft']) ? 1 : 0
                        ]);
                    } else {
                        # 如果指定消息没有对应事件，则创建对应事件
                        $user = Auth::user();
                        $time = $data['time'];
                        $draft = $data['draft'] ?? null;
                        $event = Event::create([
                            'title' => '定时消息',
                            'remark' => '定时消息',
                            'location' => 'n/a',
                            'contact' => 'n/a',
                            'url' => 'n/a',
                            'start' => $time,
                            'end' => $time,
                            'ispublic' => 0,
                            'iscourse' => 0,
                            'educator_id' => $user->educator ? $user->educator->id : 0,
                            'subject_id' => 0,
                            'alertable' => 0,
                            'alert_mins' => 0,
                            'user_id' => $user->id,
                            'enabled' => isset($draft) ? 1 : 0
                        ]);
                        $data['event_id'] = $event->id;
                    }
                    unset($data['draft']);
                    unset($data['time']);
                } else {
                    # 如果没有设置发送时间
                    $eventId = $message->event_id;
                    if ($eventId) {
                        # 如果指定消息已有对应事件，则删除该事件
                        Event::find($eventId)->delete();
                        $data['event_id'] = null;
                    }
                }
                # 更新消息草稿
                $message->update($data);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 将指定消息的状态更新为已读，并更新指定消息的已读数量
     *
     * @param $id
     * @param bool $read - 1:已读，0:未读
     * @return bool
     * @throws Throwable
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
     * 返回消息详情
     *
     * @param $id
     * @return Factory|View
     * @throws Throwable
     */
    function show($id) {
        
        $content = $this->detail($id);
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
                $msgBody = view('message.detail_video', ['message' => $message])->render();
                break;
            case 'file':
                $msgBody = '<a href="' . $message->{'path'} . '">下载文件</a>';
                break;
            case 'textcard':
                $msgBody = view('message.detail_textcard', ['message' => $message])->render();
                break;
            case 'mpnews':
                $msgBody = view('message.detail_mpnews', ['message' => $message])->render();
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
            'msgTitle'   => $content['title'],
            'app'        => $app,
            'msgBody'    => $msgBody,
            'sentAt'     => $content['updated_at'],
            'recipients' => implode('; ', $recipients),
            'sender'     => $content['sender'],
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
     * @throws Throwable
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
     * @throws Throwable
     */
    function send($data) {
        
        try {
            DB::transaction(function () use ($data) {
                throw_if(
                    empty($data['user_ids']) &&
                    empty($data['dept_ids']) &&
                    empty($data['app_ids']),
                    HttpStatusCode::NOT_ACCEPTABLE
                );
                $apps = App::whereIn('id', $data['app_ids'])->get()->toArray();
                $corp = School::find($this->schoolId() ?? session('schoolId'))->corp;
                throw_if(!$corp, HttpStatusCode::NOT_FOUND);
                
                if (
                    !isset($data['time']) ||
                    (isset($data['time']) && $data['time'] < date('now'))
                ) {
                    # 如果没有设置发送时间，或者设置了发送时间，但发送时间早于当前时间
                    # 则立即发送消息
                    SendMessage::dispatch($data, Auth::id(), $corp, $apps);
                } else {
                    # 如果发送时间晚于当前时间，则创建/更新消息
                    if (!isset($data['id'])) {
                        # 创建消息草稿
                        $this->store($data, false);
                    } else {
                        # 更新消息草稿
                        $id = $data['id'];
                        unset($data['id']);
                        $data['draft'] = false;
                        $this->modify($data, $id);
                    }
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
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
     * @throws Throwable
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
        # todo: 需将消息发送失败者（监护人）对应的学生用户id找出来
        list($failedUsers) = $this->targets($userIds, $deptIds);
        
        return $failedUsers->pluck('id')->toArray();
        
    }
    
    /**
     * 返回指定学生、教职员工及部门对应的消息发送对象（监护人、教职员工）
     *
     * @param $userIds
     * @param $deptIds
     * @return array
     */
    function targets($userIds = [], $deptIds = []) {
        
        # 需发送微信（或短信）消息的用户（监护人、教职员工）
        $targets = $this->realTargets(User::whereIn('id', $userIds)->get())->groupBy('subscribed');
        $targets = !empty($targets) ? $targets : array_fill(0, 2, []);
        # $users - 需记录消息发送日志的的用户（学生、教职员工）
        $users = User::whereIn(
            'id', array_unique(
            array_merge(DepartmentUser::whereIn('department_id', $deptIds)->pluck('user_id')->toArray(), $userIds)
        ))->get();
        # $mobiles - 需发送短信消息的用户（监护人、教职员工）手机号码
        $mobiles = Mobile::whereIn('user_id', $this->realTargets($users)->pluck('id')->toArray())
            ->where(['enabled' => 1, 'isdefault' => 1])->pluck('mobile')->toArray();
        
        return [$users, $targets, $mobiles];
        
    }
    
    /**
     * 返回指定学生和教职员工对应的发送对象（监护人、教职员工）
     *
     * @param $users - 需记录消息发送日志的用户（学生、教职员工）
     * @return Collection
     */
    private function realTargets(Collection $users) {
        
        $targets = Collect([]);
        foreach ($users as $user) {
            if ($user->student) {
                $user->student->custodians->each(
                    function (Custodian $custodian) use (&$targets) {
                        $targets->push($custodian->user);
                    }
                );
            } else {
                $targets->push($user);
            }
        }
        
        return $targets;
        
    }
    
    /**
     * 微信端消息中心首页
     *
     * @return bool|Factory|View|string
     * @throws Throwable
     */
    function wIndex() {
        
        $response = response()->json([
            'message' => __('messages.ok'),
        ]);
        switch (Request::method()) {
            case 'GET':
                $response = Request::has('id')
                    ? $this->read(Request::input('id')) ? response()->json() : false
                    : view('wechat.message_center.index');
                break;
            case 'POST':
                $response = $this->search();
                break;
            default:
                break;
        }
        
        return $response;
        
    }
    
    /**
     * 搜索已发或收到的消息
     *
     * @return string
     * @throws Throwable
     */
    private function search() {
        
        return Request::has('type')
            ? $this->searchMessage()
            : $this->searchTarget();
        
    }
    
    /**
     * 搜索消息
     *
     * @return string
     * @throws Throwable
     */
    private function searchMessage() {
        
        # 搜索已发或收到的消息
        $user = Auth::user();
        $userIds = [$user->id];
        if ($user->custodian) {
            $userIds = array_merge(
                $userIds, $user->custodian->students->pluck('user_id')->toArray()
            );
        }
        $keyword = '%' . Request::input('keyword') . '%';
        $type = Request::input('type');
        $messages = $this->where('content', 'like', $keyword)
            ->orWhere('title', 'like', $keyword)
            ->get()->filter(
                function (Message &$message) use ($userIds, $type) {
                    $userId = $type == 'sent' ? $message->sender->id : $message->receiver->id;
                    $message->{'realname'} = User::find($userId)->realname;
                    $message->{'created'} = $this->humanDate($message->created_at);
                    
                    return in_array($userId, $userIds);
                }
            );
        
        return view('wechat.message_center.messages', [
            'type'     => $type,
            'messages' => $messages,
        ])->render();
        
    }
    
    /**
     * 搜索发送对象（部门、用户）
     *
     * @return string
     * @throws Throwable
     */
    private function searchTarget() {
        
        # 搜索发送对象
        $user = Auth::user();
        $schoolId = $user->educator ? $user->educator->school_id : session('schoolId');
        $targets = Collect([]);
        if (Request::has('departmentId')) {
            # 返回指定部门下的所有学生及教职员工
            $targets = Department::find(Request::input('departmentId'))->users->filter(
                function (User $user) { return !$user->custodian; }
            );
            $type = 'user';
        } else {
            $keyword = Request::input('keyword');
            $type = Request::input('target');
            switch ($type) {
                case 'list':        # 返回所有可见部门
                case 'department':  # 搜索部门
                    $targets = Department::whereIn('id', $this->departmentIds($user->id, $schoolId))
                        ->get()->reject(
                            function (Department $department) use ($type, $keyword) {
                                return $type == 'department'
                                    ? mb_strpos($department->name, $keyword) === false ? true : false
                                    : false;
                            }
                        );
                    $type = 'department';
                    break;
                case 'user':        # 搜索用户（学生、教职员工）
                    $userIds = Department::find(Request::input('deptId'))->users->pluck('id')->toArray();
                    $targets = User::whereIn('id', $userIds)
                        ->where('realname', 'like', '%' . $keyword . '%')->get()->filter(
                            function (User $user) { return !$user->custodian; }
                        );
                    break;
                default:
                    break;
            }
        }
        # 已选定的发送对象（用户、部门）id
        $selectedTargetIds = null;
        if (Request::route('id')) {
            $targetIds = json_decode($this->find(Request::route('id'))->content)
                ->{$type == 'user' ? 'touser' : 'toparty'};
            if (!empty($targetIds)) {
                $selectedTargetIds = $type == 'user'
                    ? User::whereIn('userid', explode('|', $targetIds))->pluck('id')->toArray()
                    : explode('|', $targetIds);
            }
        }
        
        return view('wechat.message_center.targets', [
            'targets'           => $targets,
            'selectedTargetIds' => $selectedTargetIds,
            'type'              => $type,
        ])->render();
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */

    /**
     * 微信端创建消息
     *
     * @return Factory|JsonResponse|View|string
     * @throws Throwable
     */
    function wCreate() {
        
        if (Request::method() == 'POST') {
            return Request::has('file')
                ? $this->upload()
                : $this->search();
        }
        
        return view('wechat.message_center.create');
        
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
        $result = json_decode(
            Wechat::uploadMedia(
                $token['access_token'],
                $type,
                [
                    'file-contents' => curl_file_create(public_path($uploadedFile['path'])),
                    'filename'      => $uploadedFile['filename'],
                    'content-type'  => Constant::CONTENT_TYPE[$type],
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
     * @param $id
     * @return Factory|JsonResponse|View|string
     * @throws Throwable
     */
    function wEdit($id) {
        
        $message = $this->find($id);
        abort_if(
            !$message,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if (Request::method() == 'POST') {
            return Request::has('file')
                ? $this->upload()
                : $this->search();
        }
        
        return view('wechat.message_center.edit', [
            'message' => $message,
        ]);
        
    }
    
    /**
     * 微信端消息详情
     *
     * @param $id
     * @return Factory|JsonResponse|View|string
     * @throws Throwable
     */
    function wShow($id) {
        
        $response = response()->json([
            'message' => __('messages.ok'),
        ]);
        switch (Request::method()) {
            case 'GET':
                $message = $this->find($id);
                abort_if(
                    !$message,
                    HttpStatusCode::NOT_FOUND,
                    __('messages.message.not_found')
                );
                $response = view('wechat.message_center.show', [
                    'message' => $message,
                ]);
                break;
            case 'POST':
                if (Request::has('content')) {
                    # 保存消息回复
                    Request::merge(['user_id' => Auth::id()]);
                    $replied = (new MessageReply)->store(Request::all());
                    abort_if(
                        !$replied,
                        HttpStatusCode::BAD_REQUEST,
                        __('messages.fail')
                    );
                } else {
                    # 获取指定消息的所有回复
                    $response = view('wechat.message_center.replies', [
                        'replies' => $this->replies(
                            Request::input('id'),
                            Request::input('msl_id')
                        ),
                    ])->render();
                }
                break;
            case 'DELETE':
                $mr = MessageReply::find(Request::input('id'));
                abort_if(
                    !$mr, HttpStatusCode::NOT_FOUND,
                    __('messages.not_found')
                );
                $deleted = $mr->delete();
                abort_if(
                    !$deleted,
                    HttpStatusCode::BAD_REQUEST,
                    __('messages.del_fail')
                );
                break;
            default:
                break;
        }
        
        return $response;
        
    }
    
    /**
     * 获取指定消息的回复列表
     *
     * @param $id
     * @param $mslId
     * @return array
     */
    function replies($id, $mslId) {
        
        $user = Auth::user();
        $message = $this->find($id);
        $replies = MessageReply::whereMslId($mslId)->get();
        if ($user->id != $message->s_user_id) {
            $replies = MessageReply::whereMslId($mslId)->where('user_id', $user->id)->get();
        }
        $replyList = [];
        foreach ($replies as $reply) {
            $replyList[] = [
                'id'         => $reply->id,
                'content'    => $reply->content,
                'replied_at' => $this->humanDate($reply->created_at),
                'realname'   => $reply->user->realname,
                'avatar_url' => $reply->user->avatar_url,
            ];
        }
        
        return $replyList;
        
    }
    
}


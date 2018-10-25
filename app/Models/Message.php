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
use Illuminate\Database\Eloquent\Relations\HasOne;
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
 * @property int $media_type_id 媒体类型id
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
 * @property-read MediaType $mediaType
 * @property-read User $receiver
 * @property-read User $sender
 * @property-read Event|null $event
 * @property-read App|null $app
 * @property-read MessageSendingLog $messageSendinglog
 * @property-read WechatSms $wechatSms
 * @method static Builder|Message whereAppId($value)
 * @method static Builder|Message whereCommTypeId($value)
 * @method static Builder|Message whereMediaTypeId($value)
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
    
    use ModelTrait;
    
    protected $table = 'messages';
    
    protected $fillable = [
        'comm_type_id', 'app_id', 'msl_id', 'content',
        'serviceid', 'message_id', 'url', 'media_ids',
        's_user_id', 'r_user_id', 'message_type_id',
        'read', 'sent', 'title', 'event_id', 'media_type_id',
    ];
    
    /**
     * 返回指定消息所属的消息类型对象
     *
     * @return BelongsTo
     */
    function messageType() { return $this->belongsTo('App\Models\MessageType'); }
    
    /**
     * 返回制定消息所属的媒体类型对象
     *
     * @return BelongsTo
     */
    function mediaType() { return $this->belongsTo('App\Models\MediaType'); }
    
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
     * @return BelongsTo`
     */
    function event() { return $this->belongsTo('App\Models\Event'); }
    
    /**
     * 返回对应的企业微信应用
     *
     * @return BelongsTo
     */
    function app() { return $this->belongsTo('App\Models\App'); }
    
    /**
     * 返回所有对应的微信消息详情url
     *
     * @return HasOne
     */
    function wechatSms() { return $this->hasOne('App\Models\WechatSms'); }
    
    /**
     * 消息列表
     *
     * @return array
     */
    function index() {

        $received = Request::query('extra');
        $columns = [
            ['db' => 'Message.id', 'dt' => 0],
            [
                'db'        => 'Message.comm_type_id', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    if ($row['sent'] != 1) {
                        $type = '(' . (!$row['event_id']
                                ? sprintf(Snippet::BADGE_RED, '草稿')
                                : sprintf(Snippet::BADGE_ORANGE, '定时')
                            ) . ')';
                    }
                    
                    return CommType::find($d)->name . ($type ?? '');
                },
            ],
            [
                'db' => 'Message.media_type_id', 'dt' => 2,
                'formatter' => function ($d) {
                    return MediaType::find($d)->remark;
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
                'formatter' => function ($d, $row) {
                    if ($d) { return $d; }
                    $msl = $this->find($row['id'])->messageSendinglog;
                    
                    return sprintf(Snippet::BADGE_GRAY, ($msl ? $msl->recipient_count : '0') . ' 人');
                },
            ],
            [
                'db' => 'Message.message_type_id', 'dt' => 5,
                'formatter' => function ($d) {
                    return MessageType::find($d)->name;
                },
            ],
            [
                'db'        => 'Message.created_at', 'dt' => 6, 'dr' => true,
                'formatter' => function ($d, $row) {
                    if ($row['sent'] == 1) {
                        return $d;
                    } elseif ($row['sent'] == 2) {
                        return $this->humanDate(Event::find($row['event_id'])->start);
                    } else {
                        return sprintf(Snippet::BADGE_GRAY, '(n/a)');
                    }
                },
            ],
            [
                'db'        => 'Message.' . ($received ? 'read' : 'sent'), 'dt' => 7,
                'formatter' => function ($d, $row) use ($received) {
                    $id = $row['id'];
                    $html = '<a id="%s" title="%s" href="#"><i class="fa %s" style="margin-left: 15px;"></i></a>';
                    if ($received) {
                        $status = Snippet::status($row['read'], '已读', '未读');
                        $status .= sprintf($html, 'show_' . $id, '详情', 'fa-laptop');
                    } else {
                        $status = sprintf(Snippet::DT_STATUS, 'text-green', '已发');
                        if ($d == 0) {
                            $status = sprintf(Snippet::DT_STATUS, 'text-red', '草稿');
                        } elseif ($d == 2) {
                            $status = sprintf(Snippet::DT_STATUS, 'text-orange', '定时');
                        }
                        $status .= $d != 1
                            ? sprintf($html, 'edit_' . $id, '编辑', 'fa-edit')
                            : sprintf($html, 'show_' . $id, '详情', 'fa-laptop');
                    }
    
                    return $status . sprintf(Snippet::DT_LINK_DEL, $id);
                },
            ],
            ['db' => 'Message.' . ($received ? 'sent' : 'read'), 'dt' => 8],
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
                'table'      => 'media_types',
                'alias'      => 'MediaType',
                'type'       => 'INNER',
                'conditions' => [
                    'MediaType.id = Message.media_type_id',
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
                    'User.id = Message.' . ($received ? 's_user_id' : 'r_user_id'),
                ],
            ],
        ];
        $user = Auth::user();
        $userIds = [$user->id];
        if (in_array($user->role(), Constant::SUPER_ROLES)) {
            $userIds = array_unique(
                array_merge(
                    $this->userIds(School::find($this->schoolId())->department_id),
                    $userIds
                )
            );
        }
        $condition = !$received
            ? 'Message.r_user_id = 0 AND Message.s_user_id IN' . ' (' . implode(',', $userIds) . ')'
            : 'Message.r_user_id = ' . Auth::id();
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 编辑消息
     *
     * @param $id
     * @return array
     * @throws Exception
     */
    function edit($id) {
        
        try {
            $detail = $this->detail($id);
            $message = json_decode($detail[$detail['type']]);
            $toparty = $message->{'toparty'};
            $touser = $message->{'touser'};
            $targetIds = !empty($toparty) ? explode('|', $toparty) : [];
            $userids = !empty($touser) ? explode('|', $touser) : [];
            $users = User::whereIn('userid', $userids)->get();
            $targetsHtml = $this->targetsHtml($users, $targetIds);
            $timing = $this->find($id)->event_id ? true : false;
            $time = $timing ? date('Y-m-d H:i', strtotime($this->find($id)->event->start)) : null;
        } catch (Exception $e) {
            throw $e;
        }
        
        return [
            'selectedTargetIds' => $targetIds,
            'targets'           => $targetsHtml,
            'messageTypeId'     => $this->find($id)->message_type_id,
            'messageFormat'     => $detail['type'],
            'message'           => $message,
            'timing'            => $timing,
            'time'              => $time,
        ];
        
    }
    
    /**
     * 获取发送对象列表Html
     *
     * @param $users
     * @param $targetIds
     * @return string
     */
    function targetsHtml($users, &$targetIds) {
    
        $allowedDeptIds = $this->departmentIds(Auth::id());
        /** @var User $user */
        foreach ($users as $user) {
            $departmentId = head(
                array_intersect(
                    $allowedDeptIds,
                    $user->depts()->pluck('id')->toArray()
                )
            );
            $targetIds[] = 'user-' . $departmentId . '-' . $user->id;
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
        
        
        return $targetsHtml;
        
    }
    
    /**
     * 显示指定消息的内容
     *
     * @param $id
     * @return array
     */
    function detail($id) {
        
        $message = $this->find($id);
        $msl = $message->messageSendinglog;
        $type = MediaType::find($message->media_type_id)->name;
        if (CommType::find($message->comm_type_id)->name == '短信') {
            $type = 'sms';
        }
        
        return [
            'id'         => $message->id,
            'title'      => $message->title,
            'updated_at' => $this->humanDate($message->updated_at),
            'sender'     => User::find($message->s_user_id)->realname,
            'recipients' => $msl ? $msl->recipient_count : 0,
            'msl_id'     => $msl ? $msl->id : 0,
            'type'       => $type,
            $type        => $message->content,
        ];
        
    }
    
    /**
     * 返回消息详情
     *
     * @param $id
     * @return Factory|View
     * @throws Throwable
     */
    function show($id) {
        
        $detail = $this->detail($id);
        $type = $detail['type'];
        $message = json_decode($detail[$type]);
        $userids = explode('|', $message->{'touser'});
        $deptIds = explode('|', $message->{'toparty'});
        $recipients = array_merge(
            User::whereIn('userid', $userids)->pluck('realname')->toArray(),
            Department::whereIn('id', $deptIds)->pluck('name')->toArray()
        );
        $msgBody = '';
        $content = $message->{$type};
        switch ($type) {
            case 'text':
                $msgBody = $content->{'content'};
                break;
            case 'image':
                $msgBody = '<img src="' . $content->{'path'} . '" alt="" style="height: 200px;" />';
                break;
            case 'voice':
                $msgBody = '<a href="' . $content->{'path'} . '">下载语音</a>';
                break;
            case 'video':
                $msgBody = view('message.detail_video', ['message' => $content])->render();
                break;
            case 'file':
                $msgBody = '<a href="' . $content->{'path'} . '">下载文件</a>';
                break;
            case 'textcard':
                $msgBody = view('message.detail_textcard', ['message' => $content])->render();
                break;
            case 'mpnews':
                $msgBody = view('message.detail_mpnews', ['message' => $content])->render();
                break;
            case 'sms':
                $msgBody = $content;
                break;
            default:
                break;
        }
        
        return view('message.detail', [
            'msgTitle'   => $detail['title'],
            'commType'   => CommType::find($this->find($id)->comm_type_id)->name,
            'msgBody'    => $msgBody,
            'sentAt'     => $detail['updated_at'],
            'recipients' => implode('; ', $recipients),
            'sender'     => $detail['sender'],
        ])->render();
        
    }
    
    /**
     * （批量）删除消息
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定消息的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $wechatSms = WechatSms::whereMessageId($id)->first();
                if ($wechatSms) { $wechatSms->delete(); }
                $message = $this->find($id);
                if ($message->event_id) {
                    Event::find($message->event_id)->delete();
                }
                $message->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
     * @param array $data
     * @return bool
     * @throws Throwable
     */
    function send(array $data) {
        
        try {
            DB::transaction(function () use ($data) {
                if (
                    !isset($data['time']) ||
                    (isset($data['time']) && $data['time'] < date('now'))
                ) {
                    # 如果没有设置发送时间，或者设置了发送时间，
                    # 但发送时间早于当前时间, 则立即发送消息。
                    # 创建原始消息（被发送）记录
                    $msl = MessageSendingLog::create([
                        'read_count'      => 0,
                        'received_count'  => 0,
                        'recipient_count' => 0,
                    ]);
                    $data['msl_id'] = $msl->id;
                    $data['read'] = 1;
                    $data['sent'] = 1;
                    # 创建需要发送的消息
                    $message = $this->create($data);
                    SendMessage::dispatch($message);
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
                if (isset($data['time'])) {
                    $time = $data['time'];
                    unset($data['time']);
                }
                $message = $this->create($data);
                # 如果是定时消息，则创建对应的事件
                if ($time) {
                    $user = Auth::user();
                    $event = Event::create([
                        'title'       => '定时消息',
                        'remark'      => '定时消息',
                        'location'    => 'n/a',
                        'contact'     => 'n/a',
                        'url'         => 'n/a',
                        'start'       => $time,
                        'end'         => $time,
                        'ispublic'    => 0,
                        'iscourse'    => 0,
                        'educator_id' => $user->educator ? $user->educator->id : 0,
                        'subject_id'  => 0,
                        'alertable'   => 0,
                        'alert_mins'  => 0,
                        'user_id'     => $user->id,
                        'enabled'     => isset($draft) ? 1 : 0,
                    ]);
                    $message->update([
                        'event_id' => $event->id,
                        'sent' => 2 # 2 - 定时
                    ]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
                    $field = Request::input('field');
                    $status = Request::input('action') == 'enable' ? true : false;
                    foreach ($ids as $id) {
                        if ($field == 'read') {
                            $this->read($id, $status);
                        } else {
                            $this->find($id)->update([
                                'sent' => $status,
                            ]);
                        }
                    }
                });
            } catch (Exception $e) {
                throw $e;
            }
        } else {
            try {
                DB::transaction(function () use ($data, $id) {
                    $message = $this->find($id);
                    if (isset($data['time'])) {
                        # 如果设置了发送时间
                        if ($message->event_id) {
                            # 如果指定消息已有对应事件，则更新对应事件
                            Event::find($message->event_id)->update([
                                'start'   => $data['time'],
                                'end'     => $data['time'],
                                'enabled' => isset($data['draft']) ? 1 : 0,
                            ]);
                        } else {
                            # 如果指定消息没有对应事件，则创建对应事件
                            $user = Auth::user();
                            $time = $data['time'];
                            $draft = $data['draft'] ?? null;
                            $event = Event::create([
                                'title'       => '定时消息',
                                'remark'      => '定时消息',
                                'location'    => 'n/a',
                                'contact'     => 'n/a',
                                'url'         => 'n/a',
                                'start'       => $time,
                                'end'         => $time,
                                'ispublic'    => 0,
                                'iscourse'    => 0,
                                'educator_id' => $user->educator ? $user->educator->id : 0,
                                'subject_id'  => 0,
                                'alertable'   => 0,
                                'alert_mins'  => 0,
                                'user_id'     => $user->id,
                                'enabled'     => isset($draft) ? 1 : 0,
                            ]);
                            $data['event_id'] = $event->id;
                        }
                        $data['sent'] = 2;
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
                        $data['sent'] = 0;
                    }
                    # 更新消息草稿
                    $message->update($data);
                });
            } catch (Exception $e) {
                throw $e;
            }
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
     * 发送微信消息
     *
     * @param App $app - 应用详情
     * @param array $message - 消息详情
     * @return array|bool|mixed
     */
    function sendWx(App $app, array $message) {
        
        $token = Wechat::getAccessToken($app->corp->corpid, $app['secret']);
        if ($token['errcode']) {
            return $token;
        }
        $result = json_decode(
            Wechat::sendMessage($token['access_token'], $message)
        );

        return [
            'errcode'      => $result->{'errcode'},
            'errmsg'       => Constant::WXERR[$result->{'errcode'}],
            'invaliduser'  => isset($result->{'invaliduser'}) ? $result->{'invaliduser'} : '',
            'invalidparty' => isset($result->{'invalidparty'}) ? $result->{'invalidparty'} : '',
        ];
        
    }
    
    /**
     * 发送短信消息
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
     * @param Collection|User[] $users - 需要记录消息发送日志的用户（学生、教职员工)
     * @param Message $message - 被发送的消息
     * @param mixed $result - 消息发送结果
     * @param null|string $urlcode -
     * @return bool
     * @throws Throwable
     */
    function log($users, Message $message, $result, $urlcode = null) {
        
        try {
            DB::transaction(function () use ($users, $message, $result, $urlcode) {
                if (isset($urlcode)) {
                    WechatSms::create([
                        'urlcode'    => $urlcode,
                        'message_id' => $message->id,
                        'enabled'    => 1,
                    ]);
                }
                /** 创建指定用户($users)收到的消息(应用内消息） */
                $received = 0;
                $data = $message->toArray();
                $data['message_id'] = $message->id;
                $content = json_decode($data['content'], true);
                unset($data['id']);
                foreach ($users as $user) {
                    $data['r_user_id'] = $user->id;
                    $data['read'] = 0;
                    if ($content['msgtype'] != 'sms') {
                        if (!$urlcode) {
                            $failedUserIds = $this->failedUserIds(
                                $result['invaliduser'], $result['invalidparty']
                            );
                            if (!$user->student) {
                                $data['sent'] = !in_array($user->id, $failedUserIds);
                            } else {
                                $custodianUserIds = $user->student->custodians
                                    ->pluck('user_id')->toArray();
                                $data['sent'] = empty(array_intersect($custodianUserIds, $failedUserIds));
                            }
                        } else {
                            $content['sms'] = $content['msgtype'] != 'text'
                                ? config('app.url') . '/sms/' . $urlcode
                                : $content['text']['content'];
                            unset($content[$content['msgtype']]);
                            $content['msgtype'] = 'sms';
                            $content['agentid'] = '0';
                            $data['title'] = MessageType::find($data['message_type_id'])->name . '(短信)';
                            $data['content'] = json_encode($content);
                            $data['sent'] = $result > 0 ? 0 : 1;
                        }
                    } else {
                        $data['sent'] = $result > 0 ? 0 : 1;
                    }
                    $received += $data['sent'] ? 1 : 0;
                    $this->create($data);
                }
                # 更新消息发送批次记录
                $msl = $message->messageSendinglog;
                $msl->update([
                    'recipient_count' => $msl->recipient_count + $users->count(),
                    'received_count'  => $msl->received_count + $received,
                ]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 获取未收到微信消息的会员(监护人、教职员工）数量
     *
     * @param $userids
     * @param $deptIds
     * @return mixed
     */
    function failedUserIds($userids, $deptIds) {
        
        $userIds = User::whereIn('userid', explode('|', $userids))
            ->pluck('id')->toArray();
        $departmentUserIds = DepartmentUser::whereIn('department_id', explode('|', $deptIds))
            ->pluck('user_id')->toArray();
        
        return array_unique(
            array_merge(
                $userIds,
                User::whereIn('id', array_unique($departmentUserIds))->get()->filter(
                    function (User $user) { return !$user->student; }
                )->pluck('id')->toArray()
            )
        );
        
    }
    
    /**
     * 返回指定用户(学生、教职员工)及部门对应的：
     *
     * @param array $userIds - 学生及教职员工用户id列表
     * @param array $deptIds - 包含学生及教职员工的部门id列表
     *
     * @return array
     *      1. 需要发送短信的用户（监护人、教职员工）手机号码列表；
     *      2. 需要记录短信发送日志的用户列表。
     */
    function smsTargets($userIds = [], $deptIds = []) {
        
        list($realTargetUsers, $logUsers) = $this->realTargets($userIds, $deptIds);
        $mobiles = Mobile::whereIn('user_id', $realTargetUsers->pluck('id')->toArray())
            ->where(['enabled' => 1, 'isdefault' => 1])
            ->pluck('mobile')->toArray();
        
        return [$mobiles, $logUsers];
        
    }
    
    /**
     * 获取指定用户（学生、教职员工）及部门对应的消息发送对象用户（监护人、教职员工）列表
     *
     * @param array $userIds - 学生、教职员工的用户id列表
     * @param array $deptIds - 部门id列表
     * @return array
     */
    function realTargets(array $userIds, array $deptIds) {
        
        $departmentUserIds = DepartmentUser::whereIn('department_id', $deptIds)
            ->pluck('user_id')->toArray();
        $logUserIds = array_unique(array_merge($userIds, $departmentUserIds));
        $logUsers = User::whereIn('id', $logUserIds)->get();
        $targets = Collect([]);
        foreach ($logUsers as $user) {
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
        
        return [$targets, $logUsers];
        
    }
    
    /** 微信端 ------------------------------------------------------------------------------------------------------- */
    /**
     * 返回指定用户（学生、教职员工）及部门对应的:
     *
     * @param $userIds - 学生及教职员工用户id列表
     * @param $deptIds - 学生
     *
     * @return array
     *      1. 需要发送短信的用户（监护人、教职员工）手机号码列表；
     *      2. 需要记录短信发送日志的用户（学生、教职员工）列表；
     *      3. 需要发送微信的用户（监护人、教职员工，不含隶属于指定部门的用户）列表；
     *      4. 需要记录微信发送日志的用户（监护人、教职员工，包含隶属于指定部门的用户）列表
     */
    function wxTargets($userIds = [], $deptIds = []) {
        
        /**
         * @var Collection|User[] $realTargetUsers - 实际接收消息的用户(
         * @var Collection|User[] $realTargets
         */
        list($realTargetUsers, $logUsers) = $this->realTargets($userIds, $deptIds);
        $realTargets = $realTargetUsers->groupBy('subscribed');
        $wxTargets = $smsTargets = $smsLogUsers = $wxLogUsers = Collect([]);
        $logUserIds = $logUsers->pluck('id')->toArray();
        if ($realTargets->count() < 2) {
            if (array_key_exists(1, $realTargets->toArray())) {
                # 如果发送对象仅包含已关注的用户
                $wxTargets = $realTargets[1];
            } elseif (array_key_exists(0, $realTargets->toArray())) {
                # 如果发送对象仅包含未关注的用户
                $smsTargets = $realTargets[0];
            }
        } else {
            list($smsTargets, $wxTargets) = $realTargets;
        }
        $smsMobiles = Mobile::whereIn('user_id', $smsTargets->pluck('id')->toArray())
            ->where(['enabled' => 1, 'isdefault' => 1])->pluck('mobile')->toArray();
        list($smsLogUsers, $wxLogUsers) = array_map(
            function ($targets, $userIds) { return $this->logUsers($targets, $userIds); },
            [$smsTargets, $wxTargets], [$logUserIds, $logUserIds]
        );
        
        return [$smsMobiles, $smsLogUsers, $wxTargets, $wxLogUsers, $realTargetUsers];
        
    }
    
    /**
     * 获取指定发送对象（监护人、教职员工）对应的
     * 需要记录消息发送日志的用户列表
     *
     * @param $targets
     * @param $allLogUserIds
     * @return User[]|Collection
     */
    private function logUsers($targets, $allLogUserIds) {
        
        $logUserIds = [];
        foreach ($targets as $user) {
            if ($user->custodian) {
                $studentUserIds = $user->custodian->students->pluck('user_id')->toArray();
                if (!empty(array_intersect($studentUserIds, $allLogUserIds))) {
                    $logUserIds[] = array_intersect($studentUserIds, $allLogUserIds)[0];
                }
            } else {
                $logUserIds[] = $user->id;
            }
        }
        
        return User::whereIn('id', $logUserIds)->get();
        
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
                    $message->{'created'} = $this->humanDate($message->created_at);
                    if ($type == 'sent') {
                        if (!$message->sender) { return false; }
                        $userId = $message->sender->id;
                        $msl = $message->messageSendinglog;
                        $message->{'realname'} = $message->receiver
                            ? $message->receiver->realname
                            : ($msl ? $msl->recipient_count : '0') . ' 人';
                        $message->{'color'} = $message->sent ? 'green' : ($message->event_id ? 'orange' : 'red');
                        $message->{'status'} = $message->sent ? '已发送' : ($message->event_id ? '定时' : '草稿');
                        $message->{'uri'} = 'mc/' . ($message->sent ? 'show' : 'edit') . '/' . $message->id;
                    } else {
                        if (!$message->receiver) { return false; }
                        $userId = $message->receiver->id;
                        $message->{'realname'} = $message->sender ? $message->sender->realname : '(未知)';
                        $message->{'uri'} = 'mc/show/' . $message->id;
                        if (!$message->read) {
                            $message->title = '<b>' . $message->title . '</b>';
                            $message->{'created'} = '<b>' . $message->{'created'} . '</b>';
                        }
                    }
                    
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
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
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


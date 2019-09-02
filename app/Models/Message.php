<?php
namespace App\Models;

use App\Facades\{Datatable, Wechat};
use App\Helpers\{Constant, HttpStatusCode, ModelTrait, Sms, Snippet};
use App\Jobs\SendMessage;
use Carbon\Carbon;
use Doctrine\Common\Inflector\Inflector;
use Eloquent;
use Exception;
use Form;
use Html;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo, Relations\HasOne};
use Illuminate\Database\Query\Builder as QBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Auth, DB, Request};
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use ReflectionClass;
use ReflectionException;
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
 * @method static Builder|Message newModelQuery()
 * @method static Builder|Message newQuery()
 * @method static Builder|Message query()
 * @mixin Eloquent
 */
class Message extends Model {
    
    use ModelTrait;
    
    const TPL = '<div class="weui-media-box weui-media-box_text">
        <a id="%s" href="#" class="weui-cell_access" data-type="%s">
            <p class="truncate" style="font-weight: %s;">%s</p>
        </a>
        <ul class="weui-media-box__info">
            <li class="weui-media-box__info__meta" style="color: gray;">%s</li>
            <li class="weui-media-box__info__meta">%s</li>
            <li class="weui-media-box__info__meta weui-media-box__info__meta_extra">%s</li>
        </ul>
    </div>';
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
     * @throws ReflectionException
     */
    function index() {
        
        $received = Request::query('extra');
        $columns = [
            ['db' => 'Message.id', 'dt' => 0],
            [
                'db'        => 'Message.title', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    return '[' . MediaType::find($row['media_type_id'])->remark . ']' . '&nbsp;' . mb_substr($d, 0, 10);
                },
            ],
            [
                'db'        => 'Message.msl_id', 'dt' => 2,
                'formatter' => function ($d) {
                    return $d ? $d : sprintf(Snippet::BADGE, 'text-gray', '(n/a)');
                },
            ],
            [
                'db'        => 'Message.comm_type_id', 'dt' => 3,
                'formatter' => function ($d, $row) {
                    $row['sent'] == 1 ?:
                        $type = '(' . (!$row['event_id']
                                ? sprintf(Snippet::BADGE, 'text-red', '草稿')
                                : sprintf(Snippet::BADGE, 'text-orange', '定时')
                            ) . ')';
                    
                    return CommType::find($d)->name . ($type ?? '');
                },
            ],
            [
                'db'        => 'Message.media_type_id', 'dt' => 4,
                'formatter' => function ($d) {
                    $mt = MediaType::find($d);
                    
                    return Constant::MEDIA_TYPE_ICONS[$mt->name] . '&nbsp;' . $mt->remark;
                },
            ],
            [
                'db'        => 'Message.message_type_id', 'dt' => 5,
                'formatter' => function ($d) {
                    return MessageType::find($d)->name;
                },
            ],
            [
                'db'        => 'User.realname', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    if ($d) return $d;
                    $msl = $this->find($row['id'])->messageSendinglog;
                    
                    return sprintf(
                        Snippet::BADGE,
                        'text-gray',
                        ($msl ? $msl->recipient_count : '0') . ' 人'
                    );
                },
            ],
            [
                'db'        => 'Message.created_at', 'dt' => 7, 'dr' => true,
                'formatter' => function ($d, $row) {
                    return $row['sent'] == 1 ? $d
                        : ($row['sent'] == 2
                            ? $this->humanDate(Event::find($row['event_id'])->start)
                            : sprintf(Snippet::BADGE, 'text-gray', '(n/a)')
                        );
                },
            ],
            [
                'db'        => 'Message.' . ($received ? 'read' : 'sent'), 'dt' => 8,
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
                    
                    return $status . sprintf(Snippet::DT_ANCHOR, $id, '删除', 'fa-remove text-red');
                },
            ],
            ['db' => 'Message.' . ($received ? 'sent' : 'read'), 'dt' => 9],
            ['db' => 'Message.content', 'dt' => 10],
            ['db' => 'Message.app_id', 'dt' => 11],
            ['db' => 'Message.event_id', 'dt' => 12],
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
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 返回短消息列表
     * 教职员工、学校以或代理人的短信发送记录
     *
     * @param $sender
     * @param $senderid
     * @return array
     */
    function sms($sender, $senderid) {
        
        $columns = [
            ['db' => 'Message.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Message.created_at', 'dt' => 2, 'dr' => true],
            [
                'db'        => 'Message.content', 'dt' => 3,
                'formatter' => function ($d) {
                    return json_decode($d, true)['sms'];
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Message.s_user_id',
                ],
            ],
        ];
        switch ($sender) {
            case 'corp':
                $builder = Corp::find($senderid)->educators;
                break;
            case 'school':
                $builder = School::find($senderid)->educators;
                break;
            default:
                $builder = Educator::whereIn('id', [$senderid])->get();
                break;
        }
        $userIds = join(',', $builder->pluck('user_id')->toArray());
        !empty($userIds) ?: $userIds = 0;
        $condition = 'Message.comm_type_id = 2 AND s_user_id IN (' . $userIds . ')';
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
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
            $targetIds = empty($toparty = $message->{'toparty'}) ? []
                : explode('|', $toparty);
            $userids = empty($touser = $message->{'touser'}) ? []
                : explode('|', $touser);
            $users = User::whereIn('ent_attrs->userid', $userids)->get();
            $timing = $this->find($id)->event_id ? true : false;
            $time = $timing ? date('Y-m-d H:i', strtotime($this->find($id)->event->start)) : null;
        } catch (Exception $e) {
            throw $e;
        }
        
        return [
            'selectedTargetIds' => $targetIds,
            'targets'           => $this->targetsHtml($users, $targetIds),
            'messageTypeId'     => $this->find($id)->message_type_id,
            'messageFormat'     => $detail['type'],
            'message'           => $message,
            'timing'            => $timing,
            'time'              => $time,
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
        $msl = $message->messageSendinglog;
        $type = MediaType::find($message->media_type_id)->name;
        if (CommType::find($message->comm_type_id)->name == '短信') $type = 'sms';
        
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
                array_intersect($allowedDeptIds, $user->deptIds())
            );
            $targetIds[] = 'user-' . $departmentId . '-' . $user->id;
        }
        $targetsHtml = '';
        foreach ($targetIds as $targetId) {
            $paths = explode('-', $targetId);
            if (sizeof($paths) > 1) {
                $value = $paths[2];
                $name = User::find($value)->realname;
                $icon = 'fa fa-user';
            } else {
                $value = $targetId;
                $dept = Department::find($value);
                $name = $dept->name;
                $icon = Constant::NODE_TYPES[$dept->departmentType->name]['icon'];
            }
            $val = join(
                array_map(
                    function(HtmlString $element) { return $element->toHtml(); },
                    [
                        Html::tag('i', ' ' . $name, ['class' => $icon]),
                        Html::tag('i', '', ['class' => 'fa fa-close remove-selected']),
                        Form::hidden('selectedDepartments[]', $value)
                    ]
                )
            );
            $targetsHtml .= Form::button($val, [
                'class' => 'btn btn-flat',
                'style' => 'margin: 0 5px 5px 0;'
            ])->toHtml();
        }
        
        return $targetsHtml;
        
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
            User::whereIn('ent_attrs->userid', $userids)->pluck('realname')->toArray(),
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
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                Request::replace(['ids' => $ids]);
                $this->purge(['Message', 'WechatSms'], 'message_id');
                $this->purge(['Message'], 'message_id', 'reset');
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
                $time = $data['time'] ?? null;
                if (!$time || ($time && $time < date('now'))) {
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
                    SendMessage::dispatch([$this->create($data)], Auth::id());
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
                $message = $this->create($data);
                # 如果是定时消息，则创建对应的事件
                if ($time = $data['time'] ?? null) {
                    $user = Auth::user();
                    $event = Event::create(
                        array_combine(Constant::EVENT_FIELDS, [
                            '定时消息', '定时消息', 'n/a', 'n/a', 'n/a', $time, $time, 0, 0,
                            $user->educator ? $user->educator->id : 0, 0, 0, 0, $user->id,
                            isset($draft) ? 1 : 0,
                        ])
                    );
                    $message->update([
                        'event_id' => $event->id,
                        'sent'     => 2 # 2 - 定时
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
                    $status = Request::input('action') == 'enable' ? true : false;
                    foreach (array_values(Request::input('ids')) as $id) {
                        Request::input('field') == 'read'
                            ? $this->read($id, $status)
                            : $this->find($id)->update(['sent' => $status]);
                    }
                });
            } catch (Exception $e) {
                throw $e;
            }
        } else {
            try {
                DB::transaction(function () use ($data, $id) {
                    $message = $this->find($id);
                    if ($time = $data['time'] ?? null) {
                        # 如果设置了发送时间
                        if ($message->event_id) {
                            # 如果指定消息已有对应事件，则更新对应事件
                            Event::find($message->event_id)->update([
                                'start'   => $time,
                                'end'     => $time,
                                'enabled' => isset($data['draft']) ? 1 : 0,
                            ]);
                        } else {
                            # 如果指定消息没有对应事件，则创建对应事件
                            $user = Auth::user();
                            $draft = $data['draft'] ?? null;
                            $event = Event::create(
                                array_combine(Constant::EVENT_FIELDS, [
                                    '定时消息', '定时消息', 'n/a', 'n/a', 'n/a', $time, $time, 0, 0,
                                    $user->educator ? $user->educator->id : 0, 0, 0, 0, $user->id,
                                    isset($draft) ? 1 : 0,
                                ])
                            );
                            $data['event_id'] = $event->id;
                        }
                        $data['sent'] = 2;
                    } else {
                        # 如果没有设置发送时间
                        if ($eventId = $message->event_id) {
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
        
        abort_if(
            !($message = $this->find($id)),
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        try {
            DB::transaction(function () use ($message, $id, $read) {
                $message->update(['read' => $read ? 1 : 0]);
                if ($msl = MessageSendingLog::find($message->msl_id)) {
                    $msl->read_count += ($read ? 1 : -1);
                    $msl->save();
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 发送微信消息
     *
     * @param Message $message - 消息对象
     * @param array $content - 消息内容
     * @return array|bool|mixed
     */
    function sendWx(Message $message, array $content) {
        
        $app = $message->app;
        $base = $app->category == 1 ? 'ent' : 'pub';
        $result = json_decode(
            Wechat::invoke(
                $base,
                'message',
                $base == 'ent' ? 'send' : ($content['msgtype'] == 'tpl' ? 'template/send' : 'mass/sendall'),
                [Wechat::token($base, $base == 'ent' ? $app->corp->corpid : $app->appid, $app->appsecret)],
                $content
            ), true
        );
        
        return [
            'errcode'      => ($errcode = $result['errcode'] ?? 0),
            'errmsg'       => Constant::WXERR[$errcode],
            'invaliduser'  => $result['invaliduser'] ?? '',
            'invalidparty' => $result['invalidparty'] ?? '',
        ];
        
    }
    
    /**
     * 发送短信消息
     *
     * @param array $mobiles
     * @param string $content
     * @param $userId
     * @throws Throwable
     */
    function sendSms(array $mobiles, $content, $userId) {
        
        try {
            DB::transaction(function () use ($mobiles, $content, $userId) {
                throw_if(
                    !$educator = Educator::whereUserId($userId)->first(),
                    new Exception(__('messages.message.sms_send_failed'))
                );
                $school = $educator->school;
                $content .= $school->signature;
                $count = sizeof($mobiles) * ceil(mb_strlen($content) / $school->sms_len);
                throw_if(
                    $educator->sms_balance < $count,
                    new Exception(__('messages.sms_charge.insufficient'))
                );
                $submitted = (new Sms)->invoke('BatchSend2', [
                    join(',', $mobiles), $content, 'cell', '', '',
                ]);
                // 提交成功后扣减(教职员工、所属学校、所属企业)余额
                $submitted < 0 ?: array_map(
                    function (Model $model) use ($count) {
                        $model->decrement('sms_balance', $count);
                    }, [$educator, $school, $school->corp]
                );
                
                return $submitted;
            });
        } catch (Exception $e) {
            throw $e;
        }
        
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
                !$urlcode ?: WechatSms::create([
                    'urlcode'    => $urlcode,
                    'message_id' => $message->id,
                    'enabled'    => 1,
                ]);
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
                                $custodianUserIds = $user->student->custodians->pluck('user_id')->toArray();
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
                            $data['content'] = json_encode($content, JSON_UNESCAPED_UNICODE);
                            $data['sent'] = $result > 0;
                        }
                    } else {
                        $data['sent'] = $result > 0;
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
     * 获取未收到微信消息的会员用户(监护人、教职员工）id
     *
     * @param $userids
     * @param $deptIds
     * @return mixed
     */
    function failedUserIds($userids, $deptIds) {
        
        [$userIds, $deptUserIds] = array_map(
            function ($name, $ids) {
                $className = 'App\\Models\\' . ucfirst($name);
                $model = (new ReflectionClass($className))->newInstance();
                $field = $name == 'user' ? 'ent_attrs->userid' : 'department_id';
                $id = $name == 'user' ? 'id' : 'user_id';
                
                return array_unique(
                    $model->whereIn($field, explode('|', $ids))->pluck($id)->toArray()
                );
            }, ['user', 'departmentUser'], [$userids, $deptIds]
        );
        
        return array_unique(
            array_merge(
                $userIds,
                User::whereIn('id', $deptUserIds)->get()->filter(
                    function (User $user) { return !$user->student; }
                )->pluck('id')->toArray()
            )
        );
        
    }
    
    /**
     * 返回消息内容及发送对象
     *
     * @param Message $message
     * @return array
     */
    function targets(Message $message) {
        
        $content = json_decode($message->content, true);
        $senderDeptIds = (new Department)->departmentIds($message->s_user_id);
        $us = User::whereIn(
            'ent_attrs->userid',
            explode('|', $content['touser'])
        );
        $dus = DepartmentUser::whereIn(
            'department_id',
            explode('|', $content['toparty'])
        );
        $userIds = array_merge(
            $us->pluck('id')->toArray(),
            $dus->pluck('user_id')->toArray()
        );
        foreach (explode('|', $content['totag']) as $tagId) {
            $tag = Tag::find($tagId);
            $userIds = array_merge($userIds, $tag->users->pluck('id')->toArray());
            foreach ($tag->departments as $department) {
                $userIds = array_merge($userIds, $department->users->pluck('id')->toArray());
            }
        }
        $users = new Collection;
        /** @var User $user */
        foreach (User::whereIn('id', array_unique($userIds))->get() as $user) {
            if (!in_array($user->role(), Constant::NON_EDUCATOR)) {
                $users->push($user);
            } elseif ($user->student) {
                if (in_array($user->student->squad->department_id, $senderDeptIds)) {
                    $user->student->custodians->each(
                        function (Custodian $custodian) use (&$users) {
                            $users->push($custodian->user);
                        }
                    );
                }
            }
        }
        if ($message->commType->name == '短信') {
            $mobiles = $users;
        } else {
            [$mobiles, $members] = $users->groupBy(
                ($message->app->category == 1 ? 'ent' : 'pub') . '_attrs->subscribed'
            );
        }
        
        return [
            $content, $mobiles->pluck('mobile')->toArray(), $members ?? null,
        ];
        
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
                $response = ($id = Request::input('id'))
                    ? ($this->read($id) ? response()->json() : false)
                    : view('wechat.message_center.index');
                break;
            case 'POST':
                $response = $this->msgList();
                break;
            default:
                break;
        }
        
        return $response;
        
    }
    
    /**
     * 返回消息列表(微信端)
     *
     * @return string
     */
    private function msgList() {
        
        $msgList = '';
        /** @var Message $message */
        foreach ($this->messages() as $message) {
            if ($message->s_user_id == Auth::id() && !$message->r_user_id) {
                $direction = '发件';
                $color = $message->sent ? 'primary' : ($message->event_id ? 'warning' : 'error');
                $status = $message->sent ? '已发' : ($message->event_id ? '定时' : '草稿');
                $stat = '接收者';
                $msl = $message->messageSendinglog;
                $value = ($msl ? $msl->recipient_count : 0) . '人';
            } else {
                $direction = '收件';
                $color = $message->read ? 'primary' : 'error';
                $status = $message->read ? '已读' : '未读';
                $stat = '发送者';
                $value = $message->sender ? $message->sender->realname : '(未知)';
            }
            $msgList .= sprintf(
                self::TPL,
                $message->id,
                $message->sent,
                $message->read ? 'normal' : 'bold',
                '[' . $message->mediaType->remark . ']' . $message->title,
                $message->messageType->name,
                sprintf(
                    '%s : <span class="color-%s">%s</span>, %s : %s',
                    $direction, $color, $status, $stat, $value
                ),
                $this->humanDate($message->created_at)
            );
        }
        
        return $msgList;
        
    }
    
    /**
     * 获取消息
     *
     * @return Message[]|Builder[]|Collection|QBuilder[]|Collection
     */
    private function messages() {
        
        $userId = Auth::id();
        $clause = '((s_user_id = ' . $userId . ' AND r_user_id = 0) OR r_user_id = ' . $userId . ')';
        $builder = $this->whereRaw($clause);
        $action = Request::input('action');
        $params = Request::input('params');
        # 消息目录(所有、收件箱、发件箱、草稿箱)
        $folder = $params['folder'] ?? 'all';
        if (in_array($folder, ['outbox', 'draft'])) {
            $sent = $folder == 'outbox' ? 1 : 0;
            $builder = $this->where([
                's_user_id' => $userId,
                'r_user_id' => 0,
                'sent'      => $sent,
            ]);
        } else {
            $folder == 'all' ?: $builder = $this->where('r_user_id', $userId);
        }
        # 消息过滤（消息类型/格式、关键词、起止日期)
        !($messageTypeId = $params['message_type_id'])
            ?: $builder = $builder->where('message_type_id', $messageTypeId);
        !($mediaTypeId = $params['media_type_id'])
            ?: $builder = $builder->where('media_type_id', $mediaTypeId);
        !($keyword = $params['keyword'])
            ?: $builder = $builder->whereRaw("(title LIKE '%{$keyword}%' OR content LIKE '%{$keyword}%')");
        $start = $params['start'] ? $params['start'] . ' 00:00:00' : null;
        $end = $params['end'] ? $params['end'] . ' 23:59:59' : null;
        if ($start && $end) {
            abort_if(
                $start > $end, HttpStatusCode::NOT_ACCEPTABLE,
                __('messages.incorrect_data_range')
            );
            $builder = $builder->whereBetween('created_at', [$start, $end]);
        } elseif ($start && !$end) {
            $builder = $builder->where('created_at', '>=', $start);
        } elseif (!$start && $end) {
            $builder = $builder->where('created_at', '<=', $end);
        }
        # 分页加载
        $page = $params['page'] ?? 1;
        $skip = $action == 'page' ? $page * 7 : 0;
        $records = $action == 'page' ? 7 : $page * 7;
        
        return $builder->orderBy('created_at', 'desc')
            ->skip($skip)->take($records)->get();
        
    }
    
    /**
     * 微信端创建消息
     *
     * @return Factory|JsonResponse|View|string
     * @throws Throwable
     */
    function wCreate() {
        
        return Request::method() == 'POST'
            ? (Request::has('file') ? $this->import() : $this->search())
            : view('wechat.message_center.create');
        
    }
    
    /**
     * 上传媒体文件
     *
     * @return JsonResponse
     * @throws Throwable
     */
    function import() {
        
        # 上传到本地后台
        abort_if(
            empty($file = Request::file('file')),
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.empty_file')
        );
        abort_if(
            !($uploadedFile = (new Media)->import($file, __('messages.message.title'))),
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            __('messages.file_upload_failed')
        );
        # 上传到企业号后台
        [$corpid, $secret] = $this->tokenParams();
        $type = Request::input('type');
        $result = json_decode(
            Wechat::invoke(
                'ent', 'media', 'upload',
                [Wechat::token('ent', $corpid, $secret), $type == 'audio' ? 'voice' : $type], [
                'file-contents' => curl_file_create(public_path($uploadedFile['path'])),
                'filename'      => $uploadedFile['filename'],
                'content-type'  => Constant::CONTENT_TYPE[$type],
                'filelength'    => $file->getSize(),
            ])
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
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 获取当前请求对应的企业号id和“通讯录同步”Secret
     *
     * @return array
     */
    private function tokenParams() {
        
        if (!session('corpId')) {
            abort_if(
                !($corpMenuId = (new Menu)->menuId(session('menuId'), '企业')),
                HttpStatusCode::BAD_REQUEST, __('messages.bad_request')
            );
            $corp = Corp::whereMenuId($corpMenuId)->first();
        } else {
            $corp = Corp::find(session('corpId'));
        }
        
        return [
            $corp->corpid,
            Wechat::syncSecret($corp->id),
        ];
        
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
        $keyword = '%' . Request::input('keyword') . '%';
        $type = Request::input('type');
        $messages = $this->where('content', 'like', $keyword)
            ->orWhere('title', 'like', $keyword)
            ->get()->filter(
                function (Message &$message) use ($type) {
                    $message->{'created'} = $this->humanDate($message->created_at);
                    if ($type == 'sent') {
                        if (!$message->sender) return false;
                        $userId = $message->sender->id;
                        $msl = $message->messageSendinglog;
                        $message->{'realname'} = $message->receiver
                            ? $message->receiver->realname
                            : ($msl ? $msl->recipient_count : '0') . ' 人';
                        $message->{'color'} = $message->sent ? 'green' : ($message->event_id ? 'orange' : 'red');
                        $message->{'status'} = $message->sent ? '已发送' : ($message->event_id ? '定时' : '草稿');
                        $message->{'uri'} = 'mc/' . ($message->sent ? 'show' : 'edit') . '/' . $message->id;
                    } else {
                        if (!$message->receiver) return false;
                        $userId = $message->receiver->id;
                        $message->{'realname'} = $message->sender ? $message->sender->realname : '(未知)';
                        $message->{'uri'} = 'mc/show/' . $message->id;
                        if (!$message->read) {
                            $message->title = '<b>' . $message->title . '</b>';
                            $message->{'created'} = '<b>' . $message->{'created'} . '</b>';
                        }
                    }
                    
                    return $userId == Auth::id();
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
        $targets = collect([]);
        $type = 'user';
        if ($departmentId = Request::input('departmentId')) {
            # 返回指定部门下的所有学生及教职员工
            $targets = Department::find($departmentId)->users->filter(
                function (User $user) { return $user->role() != '监护人'; }
            );
        } else {
            $keyword = Request::input('keyword');
            switch ($target = Request::input('target')) {
                case 'list':        # 返回所有可见部门
                case 'department':  # 搜索部门
                    $targets = Department::whereIn('id', $this->departmentIds($user->id))
                        ->get()->reject(
                            function (Department $department) use ($target, $keyword) {
                                return $target == 'department'
                                    ? mb_strpos($department->name, $keyword) === false ? true : false
                                    : false;
                            }
                        );
                    $type = 'department';
                    break;
                case 'user':        # 搜索用户（学生、教职员工）
                    $userIds = Department::find(Request::input('deptId'))->users
                        ->pluck('id')->toArray();
                    $targets = User::where('realname', 'like', '%' . $keyword . '%')
                        ->whereIn('id', $userIds)->get()->filter(
                            function (User $user) { return $user->role() != '监护人'; }
                        );
                    break;
                default:
                    break;
            }
        }
        # 已选定的发送对象（用户、部门）id
        if ($id = Request::route('id')) {
            empty($targetIds = json_decode($this->find($id)->content)->{$type == 'user' ? 'touser' : 'toparty'}) ?:
                $selectedTargetIds = ($type == 'user')
                    ? User::whereIn('ent_attrs->userid', explode('|', $targetIds))
                        ->pluck('id')->toArray()
                    : explode('|', $targetIds);
        }
        
        return view('wechat.message_center.targets', [
            'targets'           => $targets,
            'selectedTargetIds' => $selectedTargetIds ?? null,
            'type'              => $type,
        ])->render();
        
    }
    
    /**
     * @param $id
     * @return Factory|JsonResponse|View|string
     * @throws Throwable
     */
    function wEdit($id) {
        
        abort_if(
            !($message = $this->find($id)),
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return Request::method() == 'POST'
            ? Request::has('file') ? $this->import() : $this->search()
            : view('wechat.message_center.edit', ['message' => $message]);
        
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
                abort_if(
                    !($msg = $this->find($id)),
                    HttpStatusCode::NOT_FOUND,
                    __('messages.message.not_found')
                );
                $response = view('wechat.message_center.show', [
                    'msg' => $msg,
                ]);
                break;
            case 'POST':
                if (Request::has('content')) {
                    # 保存消息回复
                    Request::merge(['user_id' => Auth::id()]);
                    abort_if(
                        !((new MessageReply)->store(Request::all())),
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
                abort_if(
                    !($mr = MessageReply::find(Request::input('id'))),
                    HttpStatusCode::NOT_FOUND, __('messages.not_found')
                );
                abort_if(
                    !($mr->delete()),
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
        $replies = MessageReply::whereMslId($mslId)->get();
        $user->id == $this->find($id)->s_user_id
            ?: $replies = MessageReply::where(['msl_id' => $mslId, 'user_id' => $user->id])->get();
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
    
    /**
     * 返回Message相关view数据
     *
     * @param null $uri
     * @return array
     */
    function compose($uri = null) {
        
        $tags = (new Tag)->list();
        switch ($uri ?? Request::route()->uri) {
            case 'messages/index':
                [$optionAll, $htmlCommType, $htmlMediaType, $htmlMessageType] = $this->filters();
                $titles = [
                    '#', '标题', '批次',
                    ['title' => '通信方式', 'html' => $htmlCommType],
                    ['title' => '格式', 'html' => $htmlMediaType],
                    ['title' => '类型', 'html' => $htmlMessageType],
                ];
                
                return [
                    'titles'       => array_merge($titles, [
                        '接收人数',
                        ['title' => '发送于', 'html' => $this->htmlDTRange('发送于')],
                        [
                            'title' => '状态',
                            'html'  => $this->htmlSelect(
                                array_merge($optionAll, [0 => '草稿', 1 => '已发', 2 => '定时']), 'filter_sent'
                            ),
                        ],
                    ]),
                    'rTitles'      => array_merge($titles, [
                        '发送者',
                        ['title' => '接收于', 'html' => $this->htmlDTRange('接收于')],
                        [
                            'title' => '状态',
                            'html'  => $this->htmlSelect(
                                array_merge($optionAll, [0 => '未读', 1 => '已读']), 'filter_read'
                            ),
                        ],
                    ]),
                    'smsMaxLength' => 300,
                    'messageTypes' => MessageType::whereEnabled(1)->pluck('name', 'id'),
                    'templates'    => $templates ?? null,
                    'tags'         => $tags ?? null,
                    'batch'        => true,
                    'filter'       => true,
                ];
            case 'messages/edit/{id}':
            case 'messages/show/{id}':
                return [
                    'users'        => User::pluck('realname', 'id'),
                    'messageTypes' => MessageType::pluck('name', 'id'),
                    'commtypes'    => CommType::pluck('name', 'id'),
                    'apps'         => App::pluck('name', 'id'),
                ];
            case 'message_centers/index':
                $role = Auth::user()->role();
                $part = session('part');
                if (isset($part) && $part == 'custodian') $role = '监护人';
                
                return array_combine(
                    ['messageTypes', 'mediaTypes', 'acronym', 'canSend'],
                    [
                        array_merge([0 => '全部'], MessageType::pluck('name', 'id')->toArray()),
                        array_merge([0 => '全部'], MediaType::pluck('remark', 'id')->toArray()),
                        School::find(session('schoolId'))->corp->acronym,
                        !in_array($role, ['监护人', '学生']),
                    ]);
            case 'message_centers/show/{id}':
                $id = Request::route('id');
                $detail = $id ? $this->detail($id) : null;
                $type = $detail['type'];
                $content = json_decode($detail[$type], true);
                
                return [
                    'detail'  => $detail,
                    'content' => $content[$type],
                    'replies' => $id ? $this->replies($id, $this->find($id)->msl_id) : null,
                ];
            case 'message_centers/create':
            case 'message_centers/edit/{id?}':
                $user = Auth::user();
                $chosenTargetsHtml = '';
                $detail = $selectedDepartmentIds = $selectedUserIds = null;
                $title = $text = $url = $btntxt = $mediaId = $accept = null;
                $filename = $filepath = $mpnewsList = $timing = null;
                if (Request::route('id')) {
                    $messageId = Request::route('id');
                    $detail = $this->detail($messageId);
                    $timing = $this->find($messageId)->event_id;
                    $type = $detail['type'];
                    $message = json_decode($detail[$type]);
                    $content = $message->{$type};
                    switch ($type) {
                        case 'text':
                            $text = $content->{'content'};
                            break;
                        case 'image':
                            [$mediaId, $filename, $filepath] = $this->fileAttrs($content);
                            $accept = 'image/*';
                            break;
                        case 'voice':
                            [$mediaId, $filename, $filepath] = $this->fileAttrs($content);
                            $accept = 'audio/*';
                            break;
                        case 'video':
                            $title = $content->{'title'};
                            $text = $content->{'description'};
                            [$mediaId, $filename, $filepath] = $this->fileAttrs($content);
                            $accept = 'video/mp4';
                            break;
                        case 'file':
                            [$mediaId, $filename, $filepath] = $this->fileAttrs($content);
                            $accept = '*';
                            break;
                        case 'textcard':
                            $title = $content->{'title'};
                            $text = $content->{'description'};
                            $url = $content->{'url'};
                            $btntxt = $content->{'btntxt'};
                            break;
                        case 'mpnews':
                            $articles = $content->{'articles'};
                            $tpl = <<<HTML
                                <li id="mpnews-%s" class="weui-uploader__file" style="background-image: %s"
                                data-media-id="%s" data-author="%s" data-content="%s" data-digest="%s"
                                data-filename="%s" data-url="%s" data-image="%s" data-title="%s"></li>
                            HTML;
                            for ($i = 0; $i < sizeof($articles); $i++) {
                                $article = $articles[$i];
                                $mpnewsList .= sprintf(
                                    $tpl, $i,
                                    'url(/' . $article->{'image_url'} . ')',
                                    $article->{'thumb_media_id'},
                                    $article->{'author'},
                                    $article->{'content'},
                                    $article->{'digest'},
                                    $article->{'filename'},
                                    $article->{'content_source_url'},
                                    $article->{'image_url'},
                                    $article->{'title'}
                                );
                            }
                            break;
                        case 'sms':
                            $text = $content;
                            break;
                        default:
                            break;
                    }
                    $selectedDepartmentIds = !empty($message->{'toparty'})
                        ? explode('|', $message->{'toparty'}) : [];
                    $touser = !empty($message->{'touser'})
                        ? explode('|', $message->{'touser'}) : [];
                    $selectedUserIds = User::whereIn('ent_attrs->userid', $touser)->pluck('id')->toArray();
                    list($departmentHtml, $userHtml) = array_map(
                        function ($ids, $type) {
                            /** @noinspection HtmlUnknownTarget */
                            $tpl = '<a id="%s" class="chosen-results-item" data-uid="%s" data-type="%s">' .
                                '<img src="%s" style="%s" alt="" /></a>';
                            $html = '';
                            $imgName = $type == 'department' ? 'department.png' : 'personal.png';
                            $imgStyle = $type == 'department' ? '' : 'border-radius: 50%;';
                            foreach ($ids as $id) {
                                $html .= sprintf(
                                    $tpl, $type . '-' . $id,
                                    $id, $type, '/img/' . $imgName,
                                    $imgStyle
                                );
                            }
                            
                            return $html;
                        },
                        [$selectedDepartmentIds, $selectedUserIds], ['department', 'user']
                    );
                    $chosenTargetsHtml = $departmentHtml . $userHtml;
                }
                # 对当前用户可见的所有部门id
                $departmentIds = $this->departmentIds($user->id);
                
                return [
                    'departments'           => Department::whereIn('id', $departmentIds)->get(),
                    'messageTypes'          => MessageType::pluck('name', 'id'),
                    'msgTypes'              => [
                        'text'     => '文本',
                        'image'    => '图片',
                        'voice'    => '语音',
                        'video'    => '视频',
                        'file'     => '文件',
                        'textcard' => '卡片',
                        'mpnews'   => '图文',
                        'sms'      => '短信',
                    ],
                    'selectedMsgTypeId'     => $detail ? $detail['type'] : null,
                    'selectedDepartmentIds' => $selectedDepartmentIds,
                    'selectedUserIds'       => $selectedUserIds,
                    'chosenTargetsHtml'     => $chosenTargetsHtml,
                    'title'                 => $title,
                    'content'               => $text,
                    'url'                   => $url,
                    'btntxt'                => $btntxt,
                    'mediaId'               => $mediaId,
                    'filepath'              => $filepath,
                    'accept'                => $accept,
                    'filename'              => $filename,
                    'mpnewsList'            => $mpnewsList,
                    'timing'                => $timing,
                ];
            default:    // 短信充值 & 查询
                return [
                    'filter' => true,
                    'titles' => [
                        '#', '发送者',
                        ['title' => '发送于', 'html' => $this->htmlDTRange('发送于')],
                        '内容',
                    ],
                ];
        }
        
    }
    
    /**
     * 获取文件类消息的mediaId及filename属性值
     *
     * @param $msg
     * @return array
     */
    private function fileAttrs($msg) {
        
        $mediaId = $msg->{'media_id'};
        $filepath = $msg->{'path'};
        $paths = explode('/', $msg->{'path'});
        $filename = $paths[sizeof($paths) - 1];
        
        return [$mediaId, $filename, $filepath];
        
    }
    
    /**
     * 返回消息Datatable过滤器（通信方式、应用及消息类型）下拉列表html
     *
     * @return array
     */
    function filters() {
    
        return array_merge(
            [$optionAll = [null => '全部']],
            array_map(
                function ($table) use ($optionAll) {
                    $class = ucfirst(Inflector::camelize($table));
                    $id = Inflector::singularize($table);
                    return $this->htmlSelect(
                        $optionAll + $class::{'whereEnabled'}(1)->pluck('name', 'id'),
                        'filter_' . $id
                    );
                }, ['comm_types', 'media_types', 'message_types']
            )
        );
        
    }
    
}


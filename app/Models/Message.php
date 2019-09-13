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
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Database\Query\Builder as QBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\{Auth, DB, Request};
use Illuminate\Support\HtmlString;
use Illuminate\View\View;
use ReflectionException;
use Throwable;

/**
 * App\Models\Message 消息
 *
 * @property int $id
 * @property int $message_type_id 消息类型ID
 * @property int $media_type_id 媒体类型id
 * @property int $app_id 应用id
 * @property int $msl_id 消息发送批次id
 * @property string $title 消息标题
 * @property string $content 消息内容
 * @property string $code 消息详情代码
 * @property int $message_id 关联的消息ID
 * @property string $url HTML页面地址
 * @property int $s_user_id 发送者用户ID
 * @property int $r_user_id 接收者用户IDs
 * @property int $read 是否已读
 * @property int $sent 消息发送是否成功
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $event_id
 * @property-read MessageType $messageType
 * @property-read MediaType $mediaType
 * @property-read User $receiver
 * @property-read User $sender
 * @property-read Event|null $event
 * @property-read App|null $app
 * @property-read MessageSendingLog $messageSendinglog
 * @method static Builder|Message whereAppId($value)
 * @method static Builder|Message whereMessageTypeId($value)
 * @method static Builder|Message whereMediaTypeId($value)
 * @method static Builder|Message whereContent($value)
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereMessageId($value)
 * @method static Builder|Message whereMslId($value)
 * @method static Builder|Message whereEventId($value)
 * @method static Builder|Message whereRUserId($value)
 * @method static Builder|Message whereRead($value)
 * @method static Builder|Message whereSUserId($value)
 * @method static Builder|Message whereSent($value)
 * @method static Builder|Message whereCode($value)
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
        'message_type_id', 'media_type_id', 'app_id', 'msl_id',
        'title', 'content', 'code', 'message_id', 'url',
        's_user_id', 'r_user_id', 'read', 'sent', 'event_id'
    ];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function messageType() { return $this->belongsTo('App\Models\MessageType'); }
    
    /** @return BelongsTo */
    function mediaType() { return $this->belongsTo('App\Models\MediaType'); }
    
    /** @return BelongsTo */
    function sender() { return $this->belongsTo('App\Models\User', 's_user_id', 'id'); }
    
    /** @return BelongsTo */
    function receiver() { return $this->belongsTo('App\Models\User', 'r_user_id', 'id'); }
    
    /** @return BelongsTo */
    function messageSendinglog() {
        
        return $this->belongsTo('App\Models\MessageSendingLog', 'msl_id', 'id');
        
    }
    
    /** @return BelongsTo` */
    function event() { return $this->belongsTo('App\Models\Event'); }
    
    /** @return BelongsTo */
    function app() { return $this->belongsTo('App\Models\App'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
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
                'db'        => 'Message.event_id', 'dt' => 3,
                'formatter' => function ($d, $row) {
                    $mt = MediaType::find($row['media_type_id'])->name;
                    $ct =  $mt->name == 'sms' ? $mt->remark : '微信';
                    if ($row['sent'] != 1) {
                        $type = sprintf(
                            Snippet::BADGE,
                            !$d ? 'text-red' : 'text-orange',
                            !$d ? '草稿' : '定时'
                        );
                        $type = '(' . $type . ')';
                    }
                    
                    return $ct . ($type ?? '');
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
                        $statuses = [
                            ['red', '草稿'],
                            ['green', '已发'],
                            ['orange', '定时']
                        ];
                        [$color, $title] = $statuses[$d];
                        $status = sprintf(Snippet::DT_STATUS, 'text-' . $color, $title);
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
        ];
        $joins = [
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
            ? 'Message.r_user_id = 0 AND Message.s_user_id IN' . ' (' . join(',', $userIds) . ')'
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
            [
                'table' => 'media_types',
                'alias' => 'MediaType',
                'type' => 'INNER',
                'conditions' => [
                    'MediaType.id = Message.media_type_id'
                ]
            ]
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
        $condition = 'MediaType.name = \'sms\' AND s_user_id IN (' . $userIds . ')';
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
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
                $message = $this->create($data);
                # 如果是定时消息，则创建对应的事件
                if ($time = $data['time'] ?? null) {
                    $message->update([
                        'event_id' => $this->eventId($time, $draft),
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
     * 编辑消息
     *
     * @param $id
     * @return array
     * @throws Exception
     */
    function edit($id) {
        
        try {
            $detail = $this->detail($id);
            $content = json_decode($detail[$detail['type']], true);
            [$toparty, $touser, $totag] = array_map(
                function ($field) use ($content) {
                    $to = $content[$field] ?? null;
                    
                    return empty($to) ? [] : explode('|', $to);
                }, ['toparty', 'touser', 'totag']
            );
            $users = User::whereIn('ent_attrs->userid', $touser)->get();
            $event = $this->find($id)->event;
            $timing = $event ? true : false;
            $start = $event ? strtotime($event->start) : null;
            $time = $timing ? date('Y-m-d H:i', $start) : null;
        } catch (Exception $e) {
            throw $e;
        }
        
        return [
            'selectedTargetIds' => $toparty,
            'targets'           => $this->targetsHtml($users, $toparty),
            'messageTypeId'     => $this->find($id)->message_type_id,
            'tagIds'            => $totag,
            'templateId'        => $content['template_id'] ?? 0,
            'messageFormat'     => $detail['type'],
            'message'           => $content,
            'timing'            => $timing,
            'time'              => $time,
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
        
        try {
            DB::transaction(function () use ($data, $id) {
                if (!$id) {
                    $status = Request::input('action') == 'enable' ? true : false;
                    foreach (array_values(Request::input('ids')) as $id) {
                        Request::input('field') == 'read'
                            ? $this->read($id, $status)
                            : $this->find($id)->update(['sent' => $status]);
                    }
                } else {
                    $message = $this->find($id);
                    if ($time = $data['time'] ?? null) {
                        # 如果设置了发送时间
                        $message->event_id
                            # 如果指定消息已有对应事件，则更新对应事件
                            ? Event::find($message->event_id)->update([
                                'start'   => $time,
                                'end'     => $time,
                                'enabled' => isset($data['draft']) ? 0 : 1,
                            ])
                            : $data['event_id'] = $this->eventId(
                                $time, $data['draft'] ?? null
                            );
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
                }
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
        
        $detail = $this->detail($id);
        $type = $detail['type'];
        $message = json_decode($detail[$type]);
        $userids = explode('|', $message->{'touser'});
        $deptIds = explode('|', $message->{'toparty'});
        $recipients = array_merge(
            User::whereIn('ent_attrs->userid', $userids)->pluck('realname')->toArray(),
            Department::whereIn('id', $deptIds)->pluck('name')->toArray()
        );
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
            default:    # sms
                $msgBody = $content;
                break;
        }
        
        return view('message.detail', [
            'msgTitle'   => $detail['title'],
            'msgBody'    => $msgBody,
            'sentAt'     => $detail['updated_at'],
            'recipients' => join('; ', $recipients),
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
                $this->purge(['Message'], 'message_id', 'reset');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
                    $data['msl_id'] = MessageSendingLog::insertGetId([]);
                    $data['read'] = $data['sent'] = 1;
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
    
    /** 微信端 ------------------------------------------------------------------------------------------------------- */
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
     * 返回消息详情
     *
     * @param $code
     * @return Factory|View
     */
    function wDetail($code) {
        
        return view('wechat.message_center.detail', [
            'message' => $message = $this->where(['code' => $code])->first(),
            'content' => $this->detail($message->id),
        ]);
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
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
        # 上传到企业微信后台
        [$base, $appid, $appsecret] = $this->params();
        $type = Request::input('type');
        $type != 'audio' ?: $type = 'voice';
        $result = json_decode(
            Wechat::invoke(
                $base, 'media', 'upload',
                [Wechat::token('ent', $appid, $appsecret), $type], [
                'file-contents' => curl_file_create(public_path($uploadedFile['path'])),
                'filename'      => $uploadedFile['filename'],
                'content-type'  => Constant::CONTENT_TYPE[$type],
                'filelength'    => $file->getSize(),
            ]), true
        );
        abort_if(
            $result['errcode'] ?? 0,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            Constant::WXERR[$result['errcode']]
        );
        $uploadedFile['media_id'] = $result['media_id'];
        
        return response()->json([
            'message' => __('messages.message.uploaded'),
            'data'    => $uploadedFile,
        ]);
        
    }
    
    /**
     * 返回Message相关view数据
     *
     * @param null $uri
     * @return array
     */
    function compose($uri = null) {
        
        switch ($uri ?? Request::route()->uri) {
            case 'messages/index':
                [$nil, $htmlMediaType, $htmlMessageType] = $this->filters();
                $titles = [
                    '#', '标题', '批次',
                    ['title' => '格式', 'html' => $htmlMediaType],
                    ['title' => '类型', 'html' => $htmlMessageType],
                ];
                $school = School::find($this->schoolId());
                $app = $school->app ?? $this->corpApp($school->corp_id);
                
                return [
                    'titles'       => array_merge($titles, [
                        '接收人数',
                        ['title' => '发送于', 'html' => $this->htmlDTRange('发送于')],
                        [
                            'title' => '状态',
                            'html'  => $this->htmlSelect(
                                $nil->union(['草稿', '已发', '定时']), 'filter_sent'
                            ),
                        ],
                    ]),
                    'rTitles'      => array_merge($titles, [
                        '发送者',
                        ['title' => '接收于', 'html' => $this->htmlDTRange('接收于')],
                        [
                            'title' => '状态',
                            'html'  => $this->htmlSelect(
                                $nil->union(['未读', '已读']), 'filter_read'
                            ),
                        ],
                    ]),
                    'smsMaxLength' => 300,
                    'messageTypes' => MessageType::whereEnabled(1)->pluck('name', 'id'),
                    'templates'    => $app->templates->pluck('title', 'id'),
                    'tags'         => (new Tag)->list(),
                    'batch'        => true,
                    'filter'       => true,
                ];
            case 'message_centers/index':
                $nil = collect([0 => '全部']);
                $role = Auth::user()->role();
                $part = session('part');
                if (isset($part) && $part == 'custodian') $role = '监护人';
                
                return array_combine(
                    ['messageTypes', 'mediaTypes', 'acronym', 'canSend'],
                    [
                        $nil->union(MessageType::pluck('name', 'id')),
                        $nil->union(MediaType::pluck('remark', 'id')),
                        School::find(session('schoolId'))->corp->acronym,
                        !in_array($role, ['监护人', '学生']),
                    ]);
            case 'message_centers/show/{id}':
                $detail = $this->detail($id = Request::route('id'));
                $type = $detail['type'];
                $content = json_decode($detail[$type], true);
                
                return [
                    'detail'  => $detail,
                    'content' => $content[$type],
                    'replies' => $this->replies($id, $this->find($id)->msl_id),
                ];
            case 'message_centers/create':
            case 'message_centers/edit/{id?}':
                $user = Auth::user();
                $chosenTargetsHtml = '';
                $detail = $selectedDepartmentIds = $selectedUserIds = null;
                $title = $text = $url = $btntxt = $mediaId = $accept = null;
                $filename = $filepath = $mpnewsList = $timing = null;
                if (Request::route('id')) {
                    $id = Request::route('id');
                    $detail = $this->detail($id);
                    $timing = $this->find($id)->event_id;
                    $type = $detail['type'];
                    $message = json_decode($detail[$type]);
                    $content = $message->{$type};
                    [$text, $title, $url, $btntxt, $mediaId, $filename, $filepath] = $this->attrs($content);
                    switch ($type) {
                        case 'image':
                            $accept = 'image/*';
                            break;
                        case 'voice':
                            $accept = 'audio/*';
                            break;
                        case 'video':
                            $accept = 'video/mp4';
                            break;
                        case 'file':
                            $accept = '*';
                            break;
                        case 'sms':
                            $text = $content;
                            break;
                        case 'mpnews':
                            $articles = $content->{'articles'};
                            for ($i = 0; $i < sizeof($articles); $i++) {
                                $a = $articles[$i];
                                $mpnewsList .= Html::tag('li', '', [
                                    'id'            => 'mpnews-' . $i,
                                    'class'         => 'weui-uploader__file',
                                    'style'         => 'background-image: ' . 'url(/' . $a->{'image_url'} . ')',
                                    'data-media-id' => $a->{'thumb_media_id'},
                                    'data-author'   => $a->{'author'},
                                    'data-content'  => $a->{'content'},
                                    'data-digest'   => $a->{'digest'},
                                    'data-filename' => $a->{'filename'},
                                    'data-url'      => $a->{'content_source_url'},
                                    'data-image'    => $a->{'image_url'},
                                    'data-title'    => $a->{'title'},
                                ])->toHtml();
                            }
                            break;
                        case 'text':
                        case 'textcard':
                        default:
                            break;
                    }
                    $selectedDepartmentIds = explode('|', $message->{'toparty'});
                    $touser = explode('|', $message->{'touser'});
                    $selectedUserIds = User::whereIn('ent_attrs->userid', $touser)->pluck('id')->toArray();
                    [$departmentHtml, $userHtml] = array_map(
                        function ($ids, $type) {
                            /** @noinspection HtmlUnknownTarget */
                            $tpl = '<a id="%s" class="chosen-results-item" data-uid="%s" data-type="%s">' .
                                '<img src="%s" style="%s" alt="" /></a>';
                            $html = '';
                            $imgName = $type == 'department' ? 'department.png' : 'personal.png';
                            $imgStyle = $type == 'department' ? '' : 'border-radius: 50%;';
                            foreach ($ids as $id) {
                                // $img = Html::image('/img/' . $imgName, '', [
                                //     'style' => $imgStyle
                                // ])->toHtml();
                                // $html .= Html::tag('a', $img, [
                                //     'id' => $type . '-' . $id,
                                //     'class' => 'chosen-results-item',
                                //     'data-uid' => $id,
                                //     'data-type' => $type
                                // ])->toHtml();
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
     * 返回消息Datatable过滤器（通信方式、应用及消息类型）下拉列表html
     *
     * @return array
     */
    function filters() {
        
        return array_merge(
            [$nil = collect([null => '全部'])],
            array_map(
                function ($table) use ($nil) {
                    $id = Inflector::singularize($table);
                    $class = ucfirst(Inflector::camelize($id));
                    
                    return $this->htmlSelect(
                        $nil->union(
                            $this->model($class)->whereEnabled(1)->pluck('name', 'id')
                        ),
                        'filter_' . $id
                    );
                }, ['media_types', 'message_types']
            )
        );
        
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
        $method = $base == 'ent' ? 'send' : ($content['msgtype'] == 'tpl' ? 'template/send' : 'mass/sendall');
        $values = [Wechat::token($base, $base == 'ent' ? $app->corp->corpid : $app->appid, $app->appsecret)];
        $result = json_decode(
            Wechat::invoke($base, 'message', $method, $values, $content), true
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
     * @param Collection $mobiles
     * @param $content
     * @param $userId
     * @throws Throwable
     */
    function sendSms(Collection $mobiles, $content, $userId) {
        
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
                    $mobiles->join(','), $content, 'cell', '', '',
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
     * @return bool
     * @throws Throwable
     */
    function log($users, Message $message, $result) {
        
        try {
            DB::transaction(function () use ($users, $message, $result) {
                /** 创建指定用户($users)收到的消息(应用内消息） */
                $received = 0;
                $data = $message->toArray();
                $data['message_id'] = $message->id;
                $content = json_decode($data['content'], true);
                unset($data['id']);
                foreach ($users as $user) {
                    $data['r_user_id'] = $user->id;
                    $data['read'] = 0;
                    $fUsers = $result['invaliduser'] ?? null;
                    $fDepts = $result['invalidparty'] ?? null;
                    $data['sent'] = $content['msgtype'] != 'sms'
                        ? !$this->failedUserIds($fUsers, $fDepts)->has($user->id)
                        : $result > 0;
                    $received += $data['sent'] ? 1 : 0;
                    $this->create($data);
                }
                # 更新消息发送批次记录
                $msl = $message->messageSendinglog;
                $msl->increment('recipient_count', $users->count());
                $msl->increment('received_count', $received);
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
     * @return Collection
     */
    function failedUserIds($userids, $deptIds) {
        
        return User::whereIn('ent_attrs->userid', explode('|', $userids))->pluck('id')->merge(
            DepartmentUser::whereIn('department_id', explode('|', $deptIds))->pluck('user_id')
        );
        
    }
    
    /**
     * 返回消息内容及发送对象(手机号码、用户)
     *
     * @param Message $message
     * @return array
     */
    function targets(Message $message) {
        
        $app = $message->app;
        $platform = $app->category;
        $content = json_decode($message->content, true);
        # 获取发送对象的user_id
        # 部门、标签类对象都需要转换成用户类对象
        [$toparty, $touser, $totag] = array_map(
            function ($field) use ($content) {
                $to = $content[$field];
                
                return empty($to) ? [] : explode('|', $to);
            }, ['toparty', 'touser', 'totag']
        );
        $userIds = collect([])->merge(
            User::whereIn('ent_attrs->userid', $touser)->pluck('id')->merge(
                DepartmentUser::whereIn('department_id', $toparty)->pluck('user_id')
            )
        );
        foreach ($totag as $tagId) {
            $tag = Tag::find($tagId);
            $userIds = $userIds->merge(
                $tag->users->pluck('id')
            );
            foreach ($tag->departments as $dept) {
                $userIds = $userIds->merge(
                    $dept->users->pluck('id')
                );
            }
        }
        # 过滤发送对象（用户）
        $senderDeptIds = (new Department)->departmentIds($message->s_user_id);
        $users = User::whereIn('id', $userIds->unique())->get()->filter(
            function (User $user) use ($senderDeptIds) {
                if ($user->student) {
                    return false;
                } elseif ($user->custodian) {
                    $custodianDeptIds = collect($user->departmentIds());
                    
                    return $custodianDeptIds->intersect($senderDeptIds)->isNotEmpty();
                } else {
                    return true;
                }
            }
        );
        # 获取发送对象的手机号码、userid(企业微信)或openid(微信服务号)
        if ($message->mediaType->name == 'sms') {
            [$mobiles, $members] = [$users, collect([])];
        } else {
            $val = ($platform == 1 ? 'ent' : 'pub') . '_attrs->subscribed';
            [$mobiles, $members] = $users->groupBy($val);
        }
        $targets = collect([]);
        /** @var User $member */
        foreach ($members as $member) {
            $targets->push(
                $platform == 1
                    ? json_decode($member->ent_attrs, true)['userid']
                    : Openid::where(['user_id' => $member->id, 'app_id' => $app->id])->first()->openid
            );
        }
        $msgUrl = join('/', [config('app.url'), 'msg', $message->code]);
        $wx = $content;
        if ($platform == 1) {
            # 企业微信
            $wx['toparty'] = $wx['totag'] = '';
        } else {
            # 微信服务号
            $wx['template_id'] = Template::find($wx['template_id'])->templateid;
            $wx['url'] = $msgUrl;
            $wx['data'] = [
                'first'    => ['value' => $message->messageType->name],
                'keyword1' => ['value' => '请点击[详情]按钮查看'],
                'keyword2' => ['value' => date('Y-m-d H:i:s', time())],
                'remark'   => ['value' => ''],
            ];
        }
        $msgType = $content['msgtype'];
        $sms = $msgType == 'sms'
            ? $content['sms']
            : ($msgType == 'text' ? $content['text']['content'] : $msgUrl);
        
        return [
            $platform,
            [$sms, $mobiles],
            [$wx, $targets],
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
                    function (HtmlString $element) { return $element->toHtml(); },
                    [
                        Html::tag('i', ' ' . $name, ['class' => $icon]),
                        Html::tag('i', '', ['class' => 'fa fa-close remove-selected']),
                        Form::hidden('selectedDepartments[]', $value),
                    ]
                )
            );
            $targetsHtml .= Form::button($val, [
                'class' => 'btn btn-flat',
                'style' => 'margin: 0 5px 5px 0;',
            ])->toHtml();
        }
        
        return $targetsHtml;
        
    }
    
    /**
     * 返回指定消息的内容
     *
     * @param $id
     * @return null|array
     */
    function detail($id) {
        
        if (!$id) return null;
        $message = $this->find($id);
        $content = json_decode($message->content, true);
        $content['msgtype'] = $type = $message->mediaType->name;
        $msl = $message->messageSendinglog;
        
        return [
            'id'         => $message->id,
            'title'      => $message->title,
            'updated_at' => $this->humanDate($message->updated_at),
            'sender'     => $message->sender->realname,
            'recipients' => $msl ? $msl->recipient_count : 0,
            'msl_id'     => $msl ? $msl->id : 0,
            'type'       => $type,
            $type        => json_encode($content),
        ];
        
    }
    
    /**
     * 将指定消息的状态更新为已读，并更新指定消息的已读数量
     *
     * @param $id
     * @param bool $read - 1:已读，0:未读
     * @return bool
     * @throws Throwable
     */
    private function read($id, $read = true) {
        
        try {
            DB::transaction(function () use ($id, $read) {
                throw_if(
                    !$message = $this->find($id),
                    new Exception(__('messages.not_found'))
                );
                $message->update(['read' => $read ? 1 : 0]);
                if ($msl = MessageSendingLog::find($message->msl_id)) {
                    $msl->increment('read_count', $read ? 1 : -1);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 获取指定消息的回复列表
     *
     * @param $id
     * @param $mslId
     * @return array
     */
    private function replies($id, $mslId) {
        
        $where = ['msl_id' => $mslId];
        Auth::id() == $this->find($id)->sender->id
            ?: $where = array_merge($where, ['user_id' => Auth::id()]);
        $replies = MessageReply::where($where)->get();
        foreach ($replies as $reply) {
            $replier = $reply->user;
            $replyList[] = [
                'id'         => $reply->id,
                'content'    => $reply->content,
                'replied_at' => $this->humanDate($reply->created_at),
                'realname'   => $replier->realname,
                'avatar_url' => $replier->avatar_url,
            ];
        }
        
        return $replyList ?? [];
        
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
     * 获取文件类消息的mediaId及filename属性值
     *
     * @param $msg
     * @return array
     */
    private function attrs($msg) {
        
        $filepath = $msg->{'path'} ?? null;
        $paths = explode('/', $filepath);
        $filename = $paths[sizeof($paths) - 1];
        
        return [
            $msg->{'text'} ?? ($msg->{'description'} ?? null),
            $msg->{'title'} ?? null,
            $msg->{'url'} ?? null,
            $msg->{'btntxt'} ?? null,
            $msg->{'media_id'} ?? null,
            $filename, $filepath,
        ];
        
    }
    
    /**
     * 获取当前请求对应的平台类型（公众号、企业微信）、appid和appsecret
     *
     * @return array
     */
    private function params() {
        
        $school = School::find(session('schoolId') ?? $this->schoolId());
        $corp = $school->corp;
        
        return [
            ($app = $school->app) ? 'pub' : 'ent',
            $app ? $app->appid : $corp->corpid,
            $app ? $app->appsecret : Wechat::syncSecret($corp->id),
        ];
        
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
     * 返回消息对应的event_id
     *
     * @param $time
     * @param $draft
     * @return int
     * @throws Throwable
     */
    private function eventId($time, $draft) {
        
        throw_if(
            !$educator = Auth::user()->educator,
            new Exception(__('messages.educator.not_found'))
        );
        
        return Event::insertGetId(
            array_combine((new Event)->getFillable(), [
                $time, $time, null, isset($draft) ? 0 : 1,
            ])
        );
        
    }
    
}


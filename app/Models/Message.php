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
    
    function messageSendinglogs() { return $this->belongsTo('App\Models\MessageSendingLog', 'msl_id', 'id'); }
    
    function commType() { return $this->belongsTo('App\Models\CommType'); }
    
    /**
     * @param MessageRequest $request
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    function store(MessageRequest $request) {
        
        $input = $request->all();
        #新增一条日志记录（指定批次）
        $msl = new MessageSendingLog();
        $logId = $msl->store(count($input['r_user_id']));
        unset($msl);
        $input['msl_id'] = $logId;
        $updateUrl = [];
        try {
            foreach ($input['r_user_id'] as $receiveUser) {
                $input['r_user_id'] = $receiveUser;
                DB::transaction(function () use ($request, $input, $updateUrl) {
                    //删除原有的图片
                    self::removeMedias($request);
                    $message = self::create($input);
                    $updateUrl['url'] = url('messages/show/' . $message->id);
                    $message->update($updateUrl);
                });
            }
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
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
                    'User.id = Message.s_user_id',
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
        
        $result = [
            'statusCode' => HttpStatusCode::OK,
            'message'    => __('messages.message.message_sent'),
        ];
        $targetIds = explode(',', $data['departIds']);
        abort_if(empty($targetIds), HttpStatusCode::NOT_ACCEPTABLE, __('messages.message.empty_targets'));
        $apps = App::whereIn('id', $data['app_ids'])->get()->toArray();
        abort_if(empty($apps), HttpStatusCode::NOT_ACCEPTABLE, __('messages.message.invalid_app_list'));
        $corp = School::find($this->schoolId())->corp;
        abort_if(!$corp, HttpStatusCode::NOT_FOUND, __('messages.message.invalid_corp'));
        # 获取发送对象(部门和用户）
        $deptIds = $userIds = [];
        foreach ($targetIds as $targetId) {
            # paths[2] = user-[departmentId]-[userId]
            $paths = explode('-', $targetId);
            if (isset($paths[2])) {
                $userIds[] = $paths[2];
            } else {
                $deptIds[] = $targetId;
            }
        }
        $userIds = array_unique($userIds);
        $deptIds = array_unique($deptIds);
        
        
        if ($data['type'] == 'sms') {
            SendMessage::dispatch($data, $userIds, $deptIds, Auth::id());
        } else {
            $toUserids = implode('|', User::whereIn('id', $userIds)->pluck('userid')->toArray());
            $toParties = implode('|', $deptIds);
        }
        # 推送的所有用户以及电话
        $targets = $this->mobiles($toUserids, $deptIds);
        $title = '';
        $msl = [
            'read_count'      => 0,
            'received_count'  => 0,
            'recipient_count' => count($targets['users']),
        ];
        $mslId = MessageSendingLog::create($msl)->id;
        foreach ($apps as $app) {
            $token = Wechat::getAccessToken($corp->corpid, $app['secret']);
            abort_if($token['errcode'], HttpStatusCode::INTERNAL_SERVER_ERROR, $token['errmsg']);
            $params = [
                'touser'  => $toUserids,
                'toparty' => $toParties,
                'agentid' => $app['agentid'],
            ];
            # 短信推送
            if ($data['type'] == 'sms') {
                $code = $this->sendSms($userIds, $deptIds, $data['content']['sms']);
                $content = $data['content']['sms'] . '【成都外国语】';
                if ($code != '0' && $code != '-1') {
                    $result = [
                        'statusCode' => 200,
                        'message'    => '消息已发送！',
                    ];
                } else {
                    $result = [
                        'statusCode' => 0,
                        'message'    => '短信推送失败！',
                    ];
                
                    return response()->json($result);
                }
            } else {
                $params['msgtype'] = $data['type'];
                switch ($data['type']) {
                    case 'text' :
                        $params['text'] = ['content' => $data['content']['text']];
                        break;
                    case 'image' :
                    case 'voice' :
                        $params['image'] = ['media_id' => $data['content']['media_id']];
                        break;
                    case 'mpnews' :
                        if (isset($i)) unset($i);
                        $i['articles'][] = $data['content']['articles'];
                        $params['mpnews'] = $i;
                        $title = $data['content']['articles']['title'];
                        break;
                    case 'video' :
                        $params['video'] = $data['content']['video'];
                        $title = $data['content']['video']['title'];
                        break;
                        break;
                }
                $status = json_decode(Wechat::sendMessage($token['access_token'], $params));
                if ($status->{'errcode'}) {
                    return response()->json([
                        'statusCode' => HttpStatusCode::INTERNAL_SERVER_ERROR,
                        'message'    => Wechat::ERRMSGS[$status->{'errcode'}],
                    ]);
                }
                $content = $params[$data['type']];
                $result = [
                    'statusCode' => HttpStatusCode::OK,
                    'message'    => __('messages.message.message_sent'),
                ];
            }
            foreach ($targets['users'] as $i) {
                $commType = $data['type'] == 'sms' ? '短信' : '应用';
                $read = $data['type'] == 'sms' ? 1 : 0;
                $sent = $result['statusCode'] == 200 ? 1 : 0;
                $mediaIds = $data['media_id'] == '' ? 0 : $data['media_id'];
                $m = [
                    'comm_type_id'    => CommType::whereName($commType)->first()->id,
                    'app_id'          => $app['id'],
                    'msl_id'          => $mslId,
                    'title'           => $title,
                    'content'         => json_encode($content),
                    'serviceid'       => 0,
                    'message_id'      => 0,
                    'url'             => '',
                    'media_ids'       => $mediaIds,
                    's_user_id'       => $i->id,
                    'r_user_id'       => Auth::id(),
                    'message_type_id' => MessageType::whereName('消息通知')->first()->id,
                    'read'            => $read,
                    'sent'            => $sent,
                ];
                $this->create($m);
            }
        
        }
        if ($result['statusCode'] == 200) {
            $readCount = $data['type'] == 'sms' ? count($targets['users']) : 0;
            $receivedCount = count($targets['users']);
            MessageSendingLog::find($mslId)->update([
                'read_count'     => $readCount,
                'received_count' => $receivedCount,
            ]);
        }
        
        return true;
    }
    
    /**
     * 发送短信
     *
     * @param $touser
     * @param $toparty
     * @param $content
     * @return string
     */
    function sendSms($touser, $toparty, $content) {
        
        $items = $this->mobiles($touser, $toparty);
        $autograph = '【成都外国语】';
        $result = Wechat::batchSend(
            'LKJK004923',
            "654321@",
            implode(',', $items['mobiles']),
            $content . $autograph
        );
        
        return json_encode($result);
        
    }
    
    /**
     * 获取指定用户及部门用户对应的手机号码
     *
     * @param $userIds
     * @param $deptIds
     * @return array
     */
    function mobiles($userIds, $deptIds) {
        
        $mobiles = [];
        $users = [];
        foreach ($userIds as $userid) {
            $user = User::whereUserid($userid)->first();
            $mobile = Mobile::whereUserId($user->id)->where('enabled', 1)->first();
            if ($mobile) {
                $mobiles[] = $mobile->mobile;
                $users[] = $user;
            }
        }
        $partyUsers = (new Department())->partyUsers($deptIds);
        foreach ($partyUsers as $user) {
            $mobile = Mobile::whereUserId($user->id)->where('enabled', 1)->first();
            if ($mobile) {
                $mobiles[] = $mobile->mobile;
                $users[] = $user;
            }
        }
        
        return $result = [
            'mobiles' => $mobiles,
            'users'   => $users,
        ];
        
    }
    
    /**
     * 上传媒体文件
     *
     * @return JsonResponse
     */
    function upload() {
        
        # 上传到本地后台
        $media = new Media();
        $result = [];
        $file = Request::file('uploadFile');
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
        $result['message'] = '上传成功！';
        
        # 上传到企业号后台
        list($corpid, $secret) = $this->tokenParams();
        $message = json_decode(
            Wechat::uploadMedia(
                Wechat::getAccessToken($corpid, $secret),
                Request::input('type'),
                ['media' => curl_file_create($uploadedFile['path'])]
            )
        );
        abort_if(
            $message->{'errcode'} != 0,
            HttpStatusCode::INTERNAL_SERVER_ERROR,
            '上传微信服务器失败！'
        );
        
        $uploadedFile['media_id'] = $message->{'media_id'};
        $result['data'] = $uploadedFile;
        
        return response()->json($result);
        
    }
    
    /** 消息中心 (微信端首页) */
    function wIndex() {
        
        $user = Auth::user(); // User::whereUserid()->first();
        abort_if(
            !$user || !in_array($user->group->name, ['教职员工', '监护人']),
            HttpStatusCode::UNAUTHORIZED,
            '<h4>您无权访问此页面</h4>'
        );
        if (Request::isMethod('post')) {
            $keywords = Request::get('keywords');
            $type = Request::get('type');
            if (!empty($keywords)) {
                switch ($type) {
                    case 'send':
                        $sendMessages = Message::whereSUserId($user->id)
                            ->where('content', 'like', '%' . $keywords . '%')
                            ->orWhere('title', 'like', '%' . $keywords . '%')
                            ->get();
                        if (sizeof($sendMessages) != 0) {
                            foreach ($sendMessages as $s) {
                                $s['r_user_id'] = User::find($s['r_user_id'])->realname;
                            }
                        }
                        
                        return response([
                            'sendMessages' => $sendMessages,
                            'type'         => $type,
                        ]);
                    case 'receive':
                        $receiveMessages = Message::whereRUserId($user->id)
                            ->where('content', 'like', '%' . $keywords . '%')
                            ->orWhere('title', 'like', '%' . $keywords . '%')
                            ->get();
                        if (sizeof($receiveMessages) != 0) {
                            foreach ($receiveMessages as $r) {
                                $r['s_user_id'] = User::find($r['s_user_id'])->realname;
                            }
                        }
                        
                        return response([
                            'type'            => $type,
                            'receiveMessages' => $receiveMessages,
                        ]);
                    default:
                        break;
                }
            }
        }
        $sendMessages = $this->where('s_user_id', $user->id)
            ->get()->unique('msl_id')
            ->sortByDesc('created_at')
            ->groupBy('message_type_id');
        $receiveMessages = $this->where('r_user_id', $user->id)
            ->get()->sortByDesc('created_at')
            ->groupBy('message_type_id');
        $count = $this->where('r_user_id', $user->id)
            ->where('read', '0')
            ->count();
        
        return [
            $sendMessages,
            $receiveMessages,
            $count,
            $user->group->name == '教职员工' ? true : false,
        ];
        
    }
    
}


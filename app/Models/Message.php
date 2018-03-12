<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Facades\Wechat;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use App\Http\Requests\MessageRequest;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        'read', 'sent','title'
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
    function user() { return $this->belongsTo('App\Models\User','s_user_id','id'); }
    
    /**
     * 返回指定消息接收的用户对象
     *
     * @return BelongsTo
     */
    function receiveUser() { return $this->belongsTo('App\Models\User','r_user_id','id'); }
    
    /**
     * 获取
     *
     * @param array $classIds
     * @return Collection|static[]
     */
    function classes(array $classIds) { return Squad::whereIn('id', $classIds)->get(['id', 'name']); }

    function messageSendinglogs() { return $this->belongsTo('App\Models\MessageSendingLog','msl_id','id'); }

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
        if (!$message) { return false; }
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
            ['db' => 'Message.read', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d
                        ? sprintf(Snippet::BADGE_GREEN, '是')
                        : sprintf(Snippet::BADGE_GREEN, '否');
                },
            ],
            ['db' => 'Message.sent', 'dt' => 7,
                'formatter' => function ($d) {
                    return $d
                        ? sprintf(Snippet::BADGE_GREEN, '是')
                        : sprintf(Snippet::BADGE_GREEN, '否');
                },
            ],
            ['db' => 'Message.created_at', 'dt' => 8],
            ['db' => 'Message.updated_at', 'dt' => 9],
        ];
        $joins = [
            [
                'table' => 'comm_types',
                'alias' => 'CommType',
                'type' => 'INNER',
                'conditions' => [
                    'CommType.id = Message.comm_type_id',
                ],
            ],
            [
                'table' => 'apps',
                'alias' => 'App',
                'type' => 'INNER',
                'conditions' => [
                    'App.id = Message.app_id',
                ],
            ],
            [
                'table' => 'message_types',
                'alias' => 'MessageType',
                'type' => 'INNER',
                'conditions' => [
                    'MessageType.id = Message.message_type_id',
                ],
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Message.s_user_id',
                ],
            ],
        ];
        $condition = null;
        $role = Auth::user()->group->name;
        if ($role == '运营' ||$role == '企业' || $role == '学校') {
            $schoolId = $this->schoolId();
            $educators = Educator::whereSchoolId($schoolId)->whereEnabled(1)->get();
            $eduUserIds = [];
            $userIds = [];
            foreach ($educators as $educator) {
                $eduUserIds[] = $educator->user_id;
            }
            #当前用户自己的发送的信息
            $userIds[] = Auth::id();
            $ids = array_merge($eduUserIds, $userIds);
            #获取当前用户自己发送的信息和该学校下教职员工的信息
            $condition = 'Message.s_user_id IN' . '(' . implode(',', array_unique($ids)) . ')';
        }
        if ($role == '教职员工') {
            $condition = 'Message.r_user_id=' . Auth::id();
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }

    function sendMessage($data) {
        
        $result = [
            'statusCode' => 200,
            'message' => '',
        ];

        $obj = explode(',', $data['departIds']);
        if ($obj) {

            $apps = App::whereIn('id', $data['app_ids'])->get()->toArray();
            if (!$apps) {
                $result = [
                    'statusCode' => 0,
                    'message' => '应用不存在，请刷新页面！',
                ];
                return response()->json($result);
            }
            $corp = Corp::whereName('万浪软件')->first();
            if (!$corp) {
                $result = [
                    'statusCode' => 0,
                    'message' => '企业号不存在，请刷新页面！',
                ];
                return response()->json($result);
            }

            $depts = [];
            $users = [];
            $us = [];
            foreach ($obj as $o) {
                $item = explode('-', $o);
                if (isset($item[1])) {
                    $users[] = User::find($item[1])->userid;
                    $us[] = User::find($item[1])->id;
                } else {
                    $depts[] = $o;
                }
            }
            $touser = implode('|', $users);
            $toparty = implode('|', $depts);
            # 推送的所有用户以及电话
            $userDatas = $this->getMobiles($us, $depts);
            $title = '';

            $msl = [
                'read_count' => 0,
                'received_count' => 0,
                'recipient_count' => count($userDatas['users']),
            ];
            $id = MessageSendingLog::create($msl)->id;
            foreach ($apps as $app) {

                $token = Wechat::getAccessToken($corp->corpid, $app['secret']);
                $message = [
                    'touser' => $touser,
                    'toparty' => $toparty,
                    'agentid' => $app['agentid'],
                ];
                # 短信推送
                if ($data['type'] == 'sms') {
                    $code = $this->sendSms($us, $depts, $data['content']['sms']);
                    $content = $data['content']['sms'] . '【成都外国语】';
                    if ($code != '0' && $code != '-1') {
                        $result = [
                            'statusCode' => 200,
                            'message' => '消息已发送！',
                        ];
                    } else {
                        $result = [
                            'statusCode' => 0,
                            'message' => '短信推送失败！',
                        ];
                        return response()->json($result);

                    }
                }else{
                    $message['msgtype'] = $data['type'];
                    switch ($data['type']) {
                        case 'text' :
                            $message['text'] = ['content' => $data['content']['text']];

                            break;
                        case 'image' :
                        case 'voice' :
                            $message['image'] = ['media_id' => $data['content']['media_id']];

                        break;
                        case 'mpnews' :
                            if(isset($i)) unset($i);
                            $i['articles'][] = $data['content']['articles'];
                            $message['mpnews'] = $i;
                            $title = $data['content']['articles']['title'];
                            break;
                        case 'video' :
                            $message['video'] = $data['content']['video'];
                            $title = $data['content']['video']['title'];
                            break;

                            break;
                    }

                    $status = json_decode(Wechat::sendMessage($token, $message));
                    $content = $message[$data['type']];

                    if ($status->errcode == 0   ) {
                        $result = [
                            'statusCode' => 200,
                            'message' => '消息已发送！',
                        ];
                    } else {
                        $result = [
                            'statusCode' => $status->errcode,
                            'message' => '消息发送失败！',
                        ];
                        return response()->json($result);
                    }

                }
                foreach ($userDatas['users'] as $i) {
                    $comtype = $data['type'] == 'sms' ? '短信' : '应用';
                    $read = $data['type'] == 'sms' ? 1 : 0;
                    $sent = $result['statusCode'] == 200 ? 1 : 0;
                    $mediaIds = $data['media_id'] == '' ? 0 : $data['media_id'];
                    $m = [
                        'comm_type_id' => CommType::whereName($comtype)->first()->id,
                        'app_id' => $app['id'],
                        'msl_id' => $id,
                        'title' => $title,
                        'content' => json_encode($content),
                        'serviceid' => 0,
                        'message_id' => 0,
                        'url' => '',
                        'media_ids' => $mediaIds,
                        's_user_id' => $i->id,
                        'r_user_id' => Auth::id(),
                        'message_type_id' => MessageType::whereName('消息通知')->first()->id,
                        'read' => $read,
                        'sent' => $sent,
                    ];
                    $this->create($m);
                }

            }
            if ($result['statusCode'] == 200) {
                $readCount = $data['type'] == 'sms' ? count($userDatas['users']) : 0;
                $receivedCount = count($userDatas['users']);
                MessageSendingLog::find($id)->update(['read_count' => $readCount , 'received_count' => $receivedCount]);
            }
        }
        
        return response()->json($result);
        
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
        $items = $this->getMobiles($touser, $toparty);
        $autograph = '【成都外国语】';
        $result = Wechat::batchSend('LKJK004923', "654321@", implode(',', $items['mobiles']), $content . $autograph);
        return json_encode($result);
    }

    /**
     * 获取所有发送短信对象的电话
     *
     * @param $touser
     * @param $toparty
     * @return array
     */
    function getMobiles($touser, $toparty) {
        
        $mobiles = [];
        $userDatas = [];
        if ($touser) {
//             $userIds = explode('|', $touser);
            foreach ($touser as $i) {
                $user = User::find($i);
                $m = Mobile::whereUserId($i)->where('enabled', 1)->first();
                if ($m) { $mobiles[] = $m->mobile; $userDatas[] = $user;}
            }
        }
        if ($toparty) {
            // $topartyIds = explode('|', $toparty);
            $dept = new Department();
            $users = $dept->getPartyUser($toparty);
            if ($users) {
                foreach ($users as $u) {
                    $m = Mobile::whereUserId($u->id)->where('enabled', 1)->first();

                    if ($m) { $mobiles[] = $m->mobile; $userDatas[] = $u;}
                }
            }

        }
        return $result = ['mobiles' => $mobiles, 'users' => $userDatas];
    }

}


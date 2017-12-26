<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Facades\Wechat;
use App\Http\Requests\MessageRequest;
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
 * @property string $content 消息内容
 * @property string $serviceid 业务id
 * @property int $message_id 关联的消息ID
 * @property string $url HTML页面地址
 * @property string $media_ids 多媒体IDs
 * @property int $user_id 发送者用户ID
 * @property string $user_ids 接收者用户IDs
 * @property int $message_type_id 消息类型ID
 * @property int $read_count 已读数量
 * @property int $received_count 消息发送成功数
 * @property int $recipient_count 接收者数量
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|Message whereContent($value)
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereMediaIds($value)
 * @method static Builder|Message whereMessageId($value)
 * @method static Builder|Message whereMessageTypeId($value)
 * @method static Builder|Message whereReadCount($value)
 * @method static Builder|Message whereReceivedCount($value)
 * @method static Builder|Message whereRecipientCount($value)
 * @method static Builder|Message whereServiceid($value)
 * @method static Builder|Message whereUpdatedAt($value)
 * @method static Builder|Message whereUrl($value)
 * @method static Builder|Message whereUserId($value)
 * @method static Builder|Message whereUserIds($value)
 * @mixin \Eloquent
 * @property-read MessageType $messageType
 * @property-read User $user
 * @property int $comm_type_id 通信方式id
 * @property int $app_id 应用id
 * @property int $msl_id 消息发送批次id
 * @property int $s_user_id 发送者用户ID
 * @property int $r_user_id 接收者用户IDs
 * @property int $readed 是否已读
 * @property int $sent 消息发送是否成功
 * @method static Builder|Message whereAppId($value)
 * @method static Builder|Message whereCommTypeId($value)
 * @method static Builder|Message whereMslId($value)
 * @method static Builder|Message whereRUserId($value)
 * @method static Builder|Message whereReaded($value)
 * @method static Builder|Message whereSUserId($value)
 * @method static Builder|Message whereSent($value)
 * @property-read \App\Models\CommType $commType
 * @property-read MessageSendingLog $messageSendinglog
 * @property-read MessageSendingLog $messageSendinglogs
 */
class Message extends Model {

    //
    protected $table = 'messages';

    protected $fillable = [
        'comm_type_id', 'app_id', 'msl_id', 'content',
        'serviceid', 'message_id', 'url', 'media_ids',
        's_user_id', 'r_user_id', 'message_type_id', 'readed', 'sent',
    ];

    /**
     * 返回指定消息所属的消息类型对象
     *
     * @return BelongsTo
     */
    public function messageType() { return $this->belongsTo('App\Models\MessageType'); }

    /**
     * 返回指定消息所属的用户对象
     *
     * @return BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User','s_user_id','id'); }
    
    /**
     * 获取
     *
     * @param array $classIds
     * @return Collection|static[]
     */
    public function classes(array $classIds) { return Squad::whereIn('id', $classIds)->get(['id', 'name']); }

    public function messageSendinglogs() { return $this->belongsTo('App\Models\MessageSendingLog'); }

    public function commType() { return $this->belongsTo('App\Models\CommType'); }
    
    /**
     * @param MessageRequest $request
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    public function store(MessageRequest $request) {
        $input = $request->all();
        $messageSendingLog = new MessageSendingLog();
        #新增一条日志记录（指定批次）
        $logId = $messageSendingLog->store(count($input['r_user_id']));
        $input['msl_id'] = $logId;
        $updateUrl = [];
        try {
            foreach ($input['r_user_id'] as $receiveUser) {
                $input['r_user_id'] = $receiveUser;
                DB::transaction(function () use ($request, $input, $updateUrl) {
                    //删除原有的图片
                    $this->removeMedias($request);
                    $crateDate = $this->create($input);
                    $updateUrl['url'] = url('messages/show/' . $crateDate->id);
                    $crateDate->update($updateUrl);
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
    public function modify(MessageRequest $request, $id) {
        $message = $this->find($id);
        if (!$message) {
            return false;
        }
        try {
            DB::transaction(function () use ($request, $id) {
                $this->removeMedias($request);

                return $this->where('id', $id)->update($request->except('_method', '_token'));
            });

        } catch (Exception $e) {
            throw $e;
        }
        return true;
    }

    public function datatable() {

        $columns = [
            ['db' => 'Message.id', 'dt' => 0],
            ['db' => 'CommType.name as commtypename', 'dt' => 1],
            ['db' => 'App.name as appname', 'dt' => 2],
            ['db' => 'Message.msl_id', 'dt' => 3],
            ['db' => 'User.realname', 'dt' => 4],
            ['db' => 'MessageType.name as messagetypename', 'dt' => 5],
            ['db' => 'Message.readed', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d === 0 ? "否" : "是";
                },
            ],
            ['db' => 'Message.sent', 'dt' => 7,
                'formatter' => function ($d) {
                    return $d === 0 ? "否" : "是";
                },
            ],
            ['db' => 'Message.created_at', 'dt' => 8],
            ['db' => 'Message.updated_at', 'dt' => 9
            ],
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
        if($role == '教职员工'){
            $condition = 'Message.r_user_id='.Auth::id();
        }
        return Datatable::simple($this, $columns, $joins,$condition);
    }

    public function sendMessage($data) {
        $result = [
            'statusCode' => 200,
            'message' => '',
        ];

        $obj = explode(',', $data['departIds']);
        if ($obj) {
            $depts = [];
            $users = [];
            foreach ($obj as $o) {
                $item = explode('-', $o);
                if ($item[1]) {
                    $users[] = User::find($item[1])->userid;
                } else {
                    $depts[] = $o;
                }
            }
            $touser = implode('|', $users);
            $toparty = implode('|', $depts);
            $apps = App::whereIn('id', $data['app_ids'])->get()->toArray();
            if (!$apps) {
                $result = [
                    'statusCode' => 0,
                    'message' => '应用不存在，请刷新页面！',
                ];
            }
            $corp = Corp::where('name', '万浪软件')->first();
            if (!$corp) {
                $result = [
                    'statusCode' => 0,
                    'message' => '企业号不存在，请刷新页面！',
                ];
            }
            foreach ($apps as $app) {
                $token = Wechat::getAccessToken($corp->corpid, $app['secret']);
                $message = [
                    'touser' => $touser,
                    'toparty' => $toparty,
                    'agentid' => $app['agentid'],
                ];
                switch ($data['type']) {
                    case 'text' :
                        $message['text'] = ['content' => $data['content']['text']];
                        break;
                    case 'image' :
                    case 'voice' :
                        $message['image'] = ['media_id' => $data['content']['media_id']];
                        break;


                }
                $message['msgtype'] = $data['type'];
                $status = json_decode(Wechat::sendMessage($token, $message));
                if ($status->errcode == 0) {
                    $result = [
                        'statusCode' => 200,
                        'message' => '消息已发送！',
                    ];
                }else{
                    $result = [
                        'statusCode' => 0,
                        'message' => '出错！',
                    ];
                }
            }
        }
        return response()->json($result);
    }
}


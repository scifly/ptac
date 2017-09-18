<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\MessageRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Mockery\Exception;

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
 * @property-read \App\Models\MessageType $messageType
 * @property-read \App\Models\User $user
 */
class Message extends Model {
    //
    protected $table = 'messages';
    
    protected $fillable = [
        'content', 'serviceid', 'message_id',
        'url', 'media_ids', 'user_id',
        'user_ids', 'message_type_id', 'read_count',
        'received_count', 'recipient_count',
    ];
    
    /**
     * 返回指定消息所属的消息类型对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function messageType() { return $this->belongsTo('App\Models\MessageType'); }
    
    /**
     * 返回指定消息所属的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }
    
    public function classes(array $classIds) {
        
        return Squad::whereIn('id', $classIds)->get(['id', 'name']);
        
    }
    
    public function store(MessageRequest $request) {
        try {
            $exception = DB::transaction(function () use ($request) {
                //删除原有的图片
                $this->removeMedias($request);
                $this->create($request->all());
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
    }
    
    public function modify(MessageRequest $request, $id) {
        $message = $this->find($id);
        if (!$message) {
            return false;
        }
        try {
            $exception = DB::transaction(function () use ($request, $id) {
                $this->removeMedias($request);
                return $this->where('id', $id)->update($request->except('_method', '_token'));
            });
            return is_null($exception) ? true : $exception;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * @param $request
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
            Media::whereIn('id', $mediaIds)->delete();
        }
    }

    public function datatable() {
        
        $columns = [
            ['db' => 'Message.id', 'dt' => 0],
            ['db' => 'Message.content', 'dt' => 1],
            ['db' => 'Message.url', 'dt' => 2],
            ['db' => 'User.realname', 'dt' => 3],
            ['db' => 'MessageType.name', 'dt' => 4],
            ['db' => 'Message.read_count', 'dt' => 5],
            ['db' => 'Message.received_count', 'dt' => 6],
            ['db' => 'Message.recipient_count', 'dt' => 7],
            ['db' => 'Message.created_at', 'dt' => 8],
            
            [
                'db' => 'Message.updated_at', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'message_types',
                'alias' => 'MessageType',
                'type' => 'INNER',
                'conditions' => [
                    'MessageType.id = Message.message_type_id'
                ]
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Message.user_id'
                ]
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }
}

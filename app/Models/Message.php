<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

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
 */
class Message extends Model {
    //
    protected $table = 'messages';
    
    protected $fillable = [
        'content',
        'serviceid',
        'message_id',
        'url',
        'media_ids',
        'user_id',
        'user_ids',
        'message_type_id',
        'read_count',
        'received_count',
        'recipient_count',
        'created_at',
        'updated_at',
    ];
    
    public function messageType() {
        return $this->belongsTo('App\Models\MessageType');
    }
    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * @return array
     */
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
                'type'  => 'INNER',
                'conditions' => [
                    'MessageType.id = Message.message_type_id'
                ]
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type'  => 'INNER',
                'conditions' => [
                    'User.id = Message.user_id'
                ]
            ]
        ];

        return Datatable::simple($this,  $columns, $joins);
    }
}

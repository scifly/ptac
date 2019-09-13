<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\HasMany};
use Illuminate\Support\Facades\{DB, Request};
use Throwable;

/**
 * App\Models\MessageSendingLog
 *
 * @property int $id
 * @property int $read_count 已读数量
 * @property int $received_count 消息发送成功数
 * @property int $recipient_count 接收者数量
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Message[] $messages
 * @property-read Collection|ApiMessage[] $apiMessages
 * @method static Builder|MessageSendingLog whereCreatedAt($value)
 * @method static Builder|MessageSendingLog whereId($value)
 * @method static Builder|MessageSendingLog whereReadCount($value)
 * @method static Builder|MessageSendingLog whereReceivedCount($value)
 * @method static Builder|MessageSendingLog whereRecipientCount($value)
 * @method static Builder|MessageSendingLog whereUpdatedAt($value)
 * @method static Builder|MessageSendingLog newModelQuery()
 * @method static Builder|MessageSendingLog newQuery()
 * @method static Builder|MessageSendingLog query()
 * @mixin Eloquent
 * @property-read int|null $api_messages_count
 * @property-read int|null $messages_count
 */
class MessageSendingLog extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'read_count',
        'received_count',
        'recipient_count',
    ];
    
    /**
     * 返回指定消息发送记录包含的所有消息对象
     *
     * @return HasMany
     */
    function messages() { return $this->hasMany('App\Models\Message'); }
    
    /**
     * 返回指定消息发送记录包含的所有接口消息对象
     *
     * @return HasMany
     */
    function apiMessages() { return $this->hasMany('App\Models\ApiMessage'); }
    
    /**
     * 保存消息发送记录
     *
     * @param array $data
     * @return MessageSendingLog|Model|null
     */
    function store(array $data) {
        
        return $this->create($data);
        
    }
    
    /**
     * 删除消息批次
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $messageIds = Message::whereIn('msl_id', $ids)
                    ->pluck('id')->toArray();
                Request::replace(['ids' => $messageIds]);
                (new Message)->remove();
                Request::replace(['ids' => $ids]);
                $this->purge([class_basename($this), 'MessageReply'], 'msl_id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}

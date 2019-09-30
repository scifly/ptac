<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\HasMany};
use Throwable;

/**
 * App\Models\MessageLog
 *
 * @property int $id
 * @property int $views 已读数量
 * @property int $deliveries 消息发送成功数
 * @property int $recipients 接收者数量
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Message[] $messages
 * @property-read Collection|ApiMessage[] $apiMessages
 * @method static Builder|MessageLog whereCreatedAt($value)
 * @method static Builder|MessageLog whereId($value)
 * @method static Builder|MessageLog whereViews($value)
 * @method static Builder|MessageLog whereDeliveries($value)
 * @method static Builder|MessageLog whereRecipients($value)
 * @method static Builder|MessageLog whereUpdatedAt($value)
 * @method static Builder|MessageLog newModelQuery()
 * @method static Builder|MessageLog newQuery()
 * @method static Builder|MessageLog query()
 * @mixin Eloquent
 * @property-read int|null $api_messages_count
 * @property-read int|null $messages_count
 */
class MessageLog extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['views', 'deliveries', 'recipients'];
    
    /** @return HasMany */
    function messages() { return $this->hasMany('App\Models\Message'); }
    
    /** @return HasMany */
    function apiMessages() { return $this->hasMany('App\Models\ApiMessage'); }
    
    /**
     * 保存消息发送记录
     *
     * @param array $data
     * @return MessageLog|Model|null
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
        
        return $this->purge($id, [
            'purge.message_log_id' => ['ApiMessage', 'Message', 'MessageReply']
        ]);
        
    }
    
}

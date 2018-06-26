<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
 * @method static Builder|MessageSendingLog whereCreatedAt($value)
 * @method static Builder|MessageSendingLog whereId($value)
 * @method static Builder|MessageSendingLog whereReadCount($value)
 * @method static Builder|MessageSendingLog whereReceivedCount($value)
 * @method static Builder|MessageSendingLog whereRecipientCount($value)
 * @method static Builder|MessageSendingLog whereUpdatedAt($value)
 * @mixin Eloquent
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
     * 保存消息发送记录
     *
     * @param $recipientCount
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    function store($recipientCount) {
        
        try {
            DB::transaction(function () use ($recipientCount) {
                $log = self::create([
                    'read_count'      => 0,
                    'received_count'  => 0,
                    'recipient_count' => $recipientCount,
                ]);
                
                return $log->id;
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 删除消息批次
     *
     * @param null $id
     * @return bool
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定消息批次相关的所有数据
     *
     * @param $id
     * @throws Exception
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $classes = ['Message', 'MessageReply'];
                $keys = array_fill(0, sizeof($classes), 'msl_id');
                $values = array_fill(0, sizeof($classes), $id);
                array_map([$this, 'delRelated'], $keys, $classes, $values);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}

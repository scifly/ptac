<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Mockery\Exception;

/**
 * App\Models\messageSendingLogs
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $messages
 * @mixin \Eloquent
 * @property int $id
 * @property int $read_count 已读数量
 * @property int $received_count 消息发送成功数
 * @property int $recipient_count 接收者数量
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|messageSendingLogs whereCreatedAt($value)
 * @method static Builder|messageSendingLogs whereId($value)
 * @method static Builder|messageSendingLogs whereReadCount($value)
 * @method static Builder|messageSendingLogs whereReceivedCount($value)
 * @method static Builder|messageSendingLogs whereRecipientCount($value)
 * @method static Builder|messageSendingLogs whereUpdatedAt($value)
 */
class messageSendingLogs extends Model {
    
    protected $fillable = [
        'read_count',
        'received_count',
        'recipient_count',
    ];
    
    public function messages() {
        return $this->hasMany('App\Models\Message');
    }
    
    public function addMessageSendingLog($recipientCount) {
        try {
            $exception = DB::transaction(function () use ($recipientCount) {
                $log = $this->create([
                    'read_count'      => 0,
                    'received_count'  => 0,
                    'recipient_count' => $recipientCount,
                ]);
                return $log->id;
            });
            
            return is_null($exception) ? true : $exception;
        } catch (Exception $exception) {
            return false;
        }
        
    }
    
}

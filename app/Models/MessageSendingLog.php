<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\MessageSendingLog
 *
 * @property int $id
 * @property int $read_count 已读数量
 * @property int $received_count 消息发送成功数
 * @property int $recipient_count 接收者数量
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read Collection|Message[] $messages
 * @method static Builder|MessageSendingLog whereCreatedAt($value)
 * @method static Builder|MessageSendingLog whereId($value)
 * @method static Builder|MessageSendingLog whereReadCount($value)
 * @method static Builder|MessageSendingLog whereReceivedCount($value)
 * @method static Builder|MessageSendingLog whereRecipientCount($value)
 * @method static Builder|MessageSendingLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MessageSendingLog extends Model {

    protected $fillable = [
        'read_count',
        'received_count',
        'recipient_count',
    ];

    public function messages() { return $this->hasMany('App\Models\Message'); }

    /**
     * @param $recipientCount
     * @return bool
     * @throws Exception
     */
    public function addMessageSendingLog($recipientCount) {
        try {
            DB::transaction(function () use ($recipientCount) {
                $log = $this->create([
                    'read_count' => 0,
                    'received_count' => 0,
                    'recipient_count' => $recipientCount,
                ]);
                return $log->id;
            });

        } catch (Exception $e) {
            throw $e;
        }
        return true;
    }

}

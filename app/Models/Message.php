<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereMediaIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereMessageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereMessageTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereReadCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereReceivedCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereRecipientCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereServiceid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUrl($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Message whereUserIds($value)
 * @mixin \Eloquent
 */
class Message extends Model
{
    //
}

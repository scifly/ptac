<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MessageType
 *
 * @property int $id
 * @property string $name 消息类型名称
 * @property string $remark 消息类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageType whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageType whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\MessageType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class MessageType extends Model
{
    //
}

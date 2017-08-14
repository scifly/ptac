<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|MessageType whereCreatedAt($value)
 * @method static Builder|MessageType whereEnabled($value)
 * @method static Builder|MessageType whereId($value)
 * @method static Builder|MessageType whereName($value)
 * @method static Builder|MessageType whereRemark($value)
 * @method static Builder|MessageType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Message[] $message
 */
class MessageType extends Model {
    //
    protected $table = 'message_types';
    
    protected $fillable = ['name', 'remark', 'created_at', 'updated_at', 'enabled'];
    
    public function message() {
        return $this->hasMany('App\Models\Message');
    }
}

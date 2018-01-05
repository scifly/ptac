<?php

namespace App\Models;

use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MessageReply
 *
 * @property int $id
 * @property int $msl_id 所属消息批次
 * @property int $user_id 消息回复者id
 * @property string $content 回复内容
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static Builder|\App\Models\MessageReply whereContent($value)
 * @method static Builder|\App\Models\MessageReply whereCreatedAt($value)
 * @method static Builder|\App\Models\MessageReply whereId($value)
 * @method static Builder|\App\Models\MessageReply whereMslId($value)
 * @method static Builder|\App\Models\MessageReply whereUpdatedAt($value)
 * @method static Builder|\App\Models\MessageReply whereUserId($value)
 * @mixin \Eloquent
 */
class MessageReply extends Model
{
    use ModelTrait;
    
    protected $fillable = [
        'msl_id','user_id','content'
    ];
    
    /**
     *
     */
    public function user() { return $this->belongsTo('App\Models\User','user_id','id'); }
    
    /**
     * @param $data
     * @return bool
     */
    static function store($data){
        $messageReply = self::create($data);
    
        return $messageReply ? true : false;
    }
    
    
}

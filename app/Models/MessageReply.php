<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Throwable;

/**
 * App\Models\MessageReply
 *
 * @property-read User $user
 * @property-read MessageLog $msgLog
 * @mixin Eloquent
 * @property int $id
 * @property int $message_log_id 所属消息批次
 * @property int $user_id 消息回复者id
 * @property string $content 回复内容
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|MessageReply whereContent($value)
 * @method static Builder|MessageReply whereCreatedAt($value)
 * @method static Builder|MessageReply whereId($value)
 * @method static Builder|MessageReply whereMessageLogId($value)
 * @method static Builder|MessageReply whereUpdatedAt($value)
 * @method static Builder|MessageReply whereUserId($value)
 * @method static Builder|MessageReply newModelQuery()
 * @method static Builder|MessageReply newQuery()
 * @method static Builder|MessageReply query()
 */
class MessageReply extends Model {
    
    // todo: needs to be optimized
    use ModelTrait;
    
    protected $fillable = ['message_log_id', 'user_id', 'content'];
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsTo */
    function msgLog() { return $this->belongsTo('App\Models\MessageLog', 'message_log_id'); }
    
    /**
     * @param $data
     * @return bool
     */
    function store($data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * （批量）删除消息回复
     *
     * @param null $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
}

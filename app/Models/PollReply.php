<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo};
use Throwable;

/**
 * App\Models\PollReply 调查问卷答案
 *
 * @property int $id
 * @property int $user_id 参与者用户id
 * @property int $poll_topic_id 题目id
 * @property string $reply 问题答案
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\PollTopic $pollTopic
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollReply newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollReply newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollReply query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollReply whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollReply whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollReply wherePollTopicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollReply whereReply($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollReply whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollReply whereUserId($value)
 * @mixin \Eloquent
 */
class PollReply extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['user_id', 'poll_topic_id', 'reply'];
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsTo */
    function pollTopic() { return $this->belongsTo('App\Models\PollTopic'); }
    
    /**
     * 保存调查问卷答案
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新调查问卷答案
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除指定的调查问卷答案
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge(
            [class_basename($this)],
            'id', 'purge', $id
        );
        
    }
    
}

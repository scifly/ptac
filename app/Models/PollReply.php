<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;
use Throwable;

/**
 * App\Models\PollReply 调查问卷答案
 *
 * @property int $id
 * @property int $user_id 参与者用户id
 * @property int $poll_topic_id 题目id
 * @property string $reply 问题答案
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PollTopic $topic
 * @property-read User $user
 * @method static Builder|PollReply newModelQuery()
 * @method static Builder|PollReply newQuery()
 * @method static Builder|PollReply query()
 * @method static Builder|PollReply whereCreatedAt($value)
 * @method static Builder|PollReply whereId($value)
 * @method static Builder|PollReply wherePollTopicId($value)
 * @method static Builder|PollReply whereReply($value)
 * @method static Builder|PollReply whereUpdatedAt($value)
 * @method static Builder|PollReply whereUserId($value)
 * @mixin Eloquent
 */
class PollReply extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['user_id', 'poll_topic_id', 'reply'];
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsTo */
    function topic() { return $this->belongsTo('App\Models\PollTopic', 'poll_topic_id'); }
    
    /** @return array */
    function index() {
        
        return [];
        
    }
    
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
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 删除指定的调查问卷答案
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
}

<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Throwable;

/**
 * App\Models\PollQuestionnaireParticipant 调查问卷参与者
 *
 * @property int $id
 * @property int $pq_id 调查问卷ID
 * @property int $user_id 参与者用户ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PollQuestionnaire $pollquestionnaire
 * @property-read User $user
 * @property-read PollQuestionnaire $pollQuestionnaire
 * @method static Builder|PollQuestionnaireParticipant whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireParticipant whereId($value)
 * @method static Builder|PollQuestionnaireParticipant wherePqId($value)
 * @method static Builder|PollQuestionnaireParticipant whereUpdatedAt($value)
 * @method static Builder|PollQuestionnaireParticipant whereUserId($value)
 * @method static Builder|PollQuestionnaireParticipant newModelQuery()
 * @method static Builder|PollQuestionnaireParticipant newQuery()
 * @method static Builder|PollQuestionnaireParticipant query()
 * @mixin Eloquent
 */
class PollQuestionnaireParticipant extends Model {
    
    use ModelTrait;
    
    protected $table = 'poll_questionnaire_participants';
    
    protected $fillable = ['pq_id', 'user_id', 'created_at', 'updated-at'];
    
    /**
     * 返回所属的调查问卷对象
     *
     * @return BelongsTo
     */
    function pollQuestionnaire() { return $this->belongsTo('App\Models\PollQuestionnaire'); }
    
    /**
     * 返回所属的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 保存调查问卷参与者
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新调查问卷参与者
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * （批量）删除调查问卷参与者
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge([class_basename($this)], 'id', 'purge', $id);
        
    }
    
}

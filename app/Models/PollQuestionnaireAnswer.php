<?php
namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Request;

/**
 * App\Models\PollQuestionnaireAnswer 调查问卷答案
 *
 * @property int $id
 * @property int $user_id 参与者用户ID
 * @property int $pqs_id
 * @property int $pq_id 调查问卷ID
 * @property string $answer 问题答案
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PollQuestionnaireAnswer whereAnswer($value)
 * @method static Builder|PollQuestionnaireAnswer whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireAnswer whereId($value)
 * @method static Builder|PollQuestionnaireAnswer wherePqId($value)
 * @method static Builder|PollQuestionnaireAnswer wherePqsId($value)
 * @method static Builder|PollQuestionnaireAnswer whereUpdatedAt($value)
 * @method static Builder|PollQuestionnaireAnswer whereUserId($value)
 * @mixin Eloquent
 * @property-read PollQuestionnaire $pollquestionnaire
 * @property-read PollQuestionnaireSubjectChoice $pollquestionnaireChoice
 * @property-read PollQuestionnaireSubject $pollquestionnaireSubject
 * @property-read User $user
 */
class PollQuestionnaireAnswer extends Model {
    
    protected $table = 'poll_questionnaire_answers';
    
    protected $fillable = ['user_id', 'pqs_id', 'pq_id', 'answer', 'created_at', 'updated_at'];
    
    /**
     * 返回指定调查问卷答案所属的用户对象
     *
     * @return BelongsTo
     */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 返回指定调查问卷答案所属的调查问卷对象
     *
     * @return BelongsTo
     */
    function pollquestionnaire() {
        
        return $this->belongsTo('App\Models\PollQuestionnaire', 'pq_id');
        
    }
    
    /**
     * 返回指定调查问卷答案所属的调查问卷题目对象
     *
     * @return BelongsTo
     */
    function pqSubject() {
        
        return $this->belongsTo('App\Models\PollQuestionnaireSubject', 'pqs_id');
        
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
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除指定的调查问卷答案
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id = null) {
        
        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
        
    }
    
}

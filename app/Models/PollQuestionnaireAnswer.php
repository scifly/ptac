<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaireAnswer
 *
 * @property int $id
 * @property int $user_id 参与者用户ID
 * @property int $pqs_id
 * @property int $pq_id 调查问卷ID
 * @property string $answer 问题答案
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|PollQuestionnaireAnswer whereAnswer($value)
 * @method static Builder|PollQuestionnaireAnswer whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireAnswer whereId($value)
 * @method static Builder|PollQuestionnaireAnswer wherePqId($value)
 * @method static Builder|PollQuestionnaireAnswer wherePqsId($value)
 * @method static Builder|PollQuestionnaireAnswer whereUpdatedAt($value)
 * @method static Builder|PollQuestionnaireAnswer whereUserId($value)
 * @mixin \Eloquent
 * @property-read \App\Models\PollQuestionnaire $pollquestionnaire
 * @property-read \App\Models\PollQuestionnaireChoice $pollquestionnaireChoice
 * @property-read \App\Models\PollQuestionnaireSubject $pollquestionnaireSubject
 * @property-read \App\Models\User $user
 */
class PollQuestionnaireAnswer extends Model {
    
    //
    protected $table = 'poll_questionnaire_answers';
    
    protected $fillable = ['user_id', 'pqs_id', 'pq_id', 'answer', 'created_at', 'updated_at'];
    
    public function user() {
        return $this->belongsTo('App\Models\User');
    }
    
    public function pollquestionnaire() {
        return $this->belongsTo('App\Models\PollQuestionnaire');
    }
    
    public function pollquestionnaireSubject() {
        return $this->belongsTo('App\Models\PollQuestionnaireSubject');
    }
    
    public function pollquestionnaireChoice() {
        return $this->hasOne('App\Models\PollQuestionnaireChoice');
    }
}

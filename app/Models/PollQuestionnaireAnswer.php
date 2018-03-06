<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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

    function user() { return $this->belongsTo('App\Models\User'); }

    function pollquestionnaire() { return $this->belongsTo('App\Models\PollQuestionnaire'); }

    function pollquestionnaireSubject() { return $this->belongsTo('App\Models\PollQuestionnaireSubject'); }

    function pollquestionnaireChoice() { return $this->hasOne('App\Models\PollQuestionnaireSubjectChoice'); }
    
}

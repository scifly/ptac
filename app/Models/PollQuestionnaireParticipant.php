<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaireParticipant 调查问卷参与者
 *
 * @property int $id
 * @property int $pq_id 调查问卷ID
 * @property int $user_id 参与者用户ID
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|PollQuestionnaireParticipant whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireParticipant whereId($value)
 * @method static Builder|PollQuestionnaireParticipant wherePqId($value)
 * @method static Builder|PollQuestionnaireParticipant whereUpdatedAt($value)
 * @method static Builder|PollQuestionnaireParticipant whereUserId($value)
 * @mixin Eloquent
 * @property-read PollQuestionnaire $pollquestionnaire
 * @property-read User $user
 */
class PollQuestionnaireParticipant extends Model {

    protected $table = 'poll_questionnaire_participants';

    protected $fillable = ['pq_id', 'user_id', 'created_at', 'updated-at'];

    function pollquestionnaire() { return $this->belongsTo('App\Models\PollQuestionnaire'); }

    function user() { return $this->belongsTo('App\Models\User'); }
    
}

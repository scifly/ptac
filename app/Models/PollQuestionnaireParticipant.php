<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaireParticipant
 *
 * @property int $id
 * @property int $pq_id 调查问卷ID
 * @property int $user_id 参与者用户ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|PollQuestionnaireParticipant whereCreatedAt($value)
 * @method static Builder|PollQuestionnaireParticipant whereId($value)
 * @method static Builder|PollQuestionnaireParticipant wherePqId($value)
 * @method static Builder|PollQuestionnaireParticipant whereUpdatedAt($value)
 * @method static Builder|PollQuestionnaireParticipant whereUserId($value)
 * @mixin \Eloquent
 */
class PollQuestionnaireParticipant extends Model {
    //
    protected $table = 'poll_questionnaire_participants';

    protected $fillable = ['pq_id','user_id','created_at','updated-at'];

    public function pollquestionnaire()
    {
        return $this->belongsTo('App\Models\PollQuestionnaire');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaireParticipant
 *
 * @property int $id
 * @property int $pq_id 调查问卷ID
 * @property int $user_id 参与者用户ID
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireParticipant whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireParticipant whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireParticipant wherePqId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireParticipant whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireParticipant whereUserId($value)
 * @mixin \Eloquent
 */
class PollQuestionnaireParticipant extends Model
{
    //
}

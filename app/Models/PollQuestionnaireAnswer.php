<?php

namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireAnswer whereAnswer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireAnswer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireAnswer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireAnswer wherePqId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireAnswer wherePqsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireAnswer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireAnswer whereUserId($value)
 * @mixin \Eloquent
 */
class PollQuestionnaireAnswer extends Model
{
    //
}

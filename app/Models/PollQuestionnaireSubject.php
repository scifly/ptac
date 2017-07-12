<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaireSubject
 *
 * @property int $id
 * @property string $subject 题目名称
 * @property int $pq_id 调查问卷ID
 * @property int $subject_type 题目类型：0 - 单选，1 - 多选, 2 - 填空
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireSubject whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireSubject whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireSubject wherePqId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireSubject whereSubject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireSubject whereSubjectType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaireSubject whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PollQuestionnaireSubject extends Model
{
    //
}

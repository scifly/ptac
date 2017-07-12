<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PollQuestionnaire
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property int $user_id 发起者用户ID
 * @property string $name 问卷调查名称
 * @property string $start 开始时间
 * @property string $end 结束时间
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaire whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaire whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaire whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaire whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaire whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaire whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaire whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaire whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\PollQuestionnaire whereUserId($value)
 * @mixin \Eloquent
 */
class PollQuestionnaire extends Model
{
    //
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ScoreRange
 *
 * @property int $id
 * @property string $name 成绩统计项名称
 * @property string $subject_ids 成绩统计项包含的科目IDs
 * @property int $school_id 成绩统计项所属学校ID
 * @property float $start_score 成绩统计项起始分数
 * @property float $end_score 成绩统计项截止分数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled 是否统计
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreRange whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreRange whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreRange whereEndScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreRange whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreRange whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreRange whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreRange whereStartScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreRange whereSubjectIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreRange whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScoreRange extends Model
{
    //
}

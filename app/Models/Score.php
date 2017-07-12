<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Score
 *
 * @property int $id
 * @property int $student_id 学生ID
 * @property int $subject_id 科目ID
 * @property int $exam_id 考试ID
 * @property int $class_rank 班级排名
 * @property int $grade_rank 年级排名
 * @property float $score 分数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled 是否参加考试
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score whereClassRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score whereGradeRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score whereSubjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Score whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Score extends Model
{
    //
}

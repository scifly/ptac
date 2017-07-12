<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ScoreTotal
 *
 * @property int $id
 * @property int $student_id 学生ID
 * @property int $exam_id 考试ID
 * @property float $score 总分
 * @property string $subject_ids 计入总成绩的科目IDs
 * @property string $na_subject_ids 未计入总成绩的科目IDs
 * @property int $class_rank 班级排名
 * @property int $grade_rank 年级排名
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereClassRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereExamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereGradeRank($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereNaSubjectIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereScore($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereSubjectIds($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ScoreTotal extends Model
{
    //
}

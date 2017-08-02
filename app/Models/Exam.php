<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Exam
 *
 * @property int $id
 * @property string $name 考试名称
 * @property string $remark 备注
 * @property int $exam_type_id 考试类型ID
 * @property string $class_ids 参加考试的班级ID
 * @property string $subject_ids 考试科目ID
 * @property string $max_scores 科目满分
 * @property string $pass_scores 科目及格分数
 * @property string $start_date 考试开始日期
 * @property string $end_date 考试结束日期
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Exam whereClassIds($value)
 * @method static Builder|Exam whereCreatedAt($value)
 * @method static Builder|Exam whereEnabled($value)
 * @method static Builder|Exam whereEndDate($value)
 * @method static Builder|Exam whereExamTypeId($value)
 * @method static Builder|Exam whereId($value)
 * @method static Builder|Exam whereMaxScores($value)
 * @method static Builder|Exam whereName($value)
 * @method static Builder|Exam wherePassScores($value)
 * @method static Builder|Exam whereRemark($value)
 * @method static Builder|Exam whereStartDate($value)
 * @method static Builder|Exam whereSubjectIds($value)
 * @method static Builder|Exam whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\ExamType $examType
 */
class Exam extends Model {

    protected $table='exams';

    protected $fillable=[
        'name',
        'remark',
        'exam_type_id',
        'class_ids',
        'subject_ids',
        'max_scores',
        'pass_scores',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'enabled'
    ];

    public function examType()
    {
        return $this->belongsTo('App\models\ExamType');
    }
}

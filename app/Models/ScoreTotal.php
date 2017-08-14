<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

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
 * @method static Builder|ScoreTotal whereClassRank($value)
 * @method static Builder|ScoreTotal whereCreatedAt($value)
 * @method static Builder|ScoreTotal whereExamId($value)
 * @method static Builder|ScoreTotal whereGradeRank($value)
 * @method static Builder|ScoreTotal whereId($value)
 * @method static Builder|ScoreTotal whereNaSubjectIds($value)
 * @method static Builder|ScoreTotal whereScore($value)
 * @method static Builder|ScoreTotal whereStudentId($value)
 * @method static Builder|ScoreTotal whereSubjectIds($value)
 * @method static Builder|ScoreTotal whereUpdatedAt($value)
 * @mixin \Eloquent
 * 总分数
 * @property int $enabled
 * @property-read \App\Models\Exam $exam
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\Subject $subjects
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\ScoreTotal whereEnabled($value)
 */
class ScoreTotal extends Model {

    protected $table = 'score_totals';
    protected $fillable = [
        'student_id',
        'exam_id',
        'score',
        'subject_ids',
        'na_subject_ids',
        'class_rank',
        'grade_rank',
        'enabled'
    ];

    public function student() {
        return $this->belongsTo('App\Models\Student');
    }

    public function exam() {
        return $this->belongsTo('App\Models\Exam');
    }

    function subjects() {
        return $this->belongsTo('App\Models\Subject');
    }

    public function  datatable() {

        $columns = [
            ['db' => 'ScoreTotal.id', 'dt' => 0],
            ['db' => 'Student.student_number', 'dt' => 1],
            ['db' => 'User.realname', 'dt' => 2],
            ['db' => 'Exam.name as examname', 'dt' => 3],
            ['db' => 'ScoreTotal.score', 'dt' => 4],
            ['db' => 'ScoreTotal.class_rank', 'dt' => 5],
            ['db' => 'ScoreTotal.grade_rank', 'dt' => 6],
            ['db' => 'ScoreTotal.created_at', 'dt' => 7],
            [
                'db' => 'ScoreTotal.updated_at', 'dt' => 8,
                'formatter' => function ($d, $row) {

                    $id = $row['id'];
                    $showLink = $d . sprintf(Datatable::DT_LINK_SHOW, $id);

                    return Datatable::DT_SPACE . $showLink;

                }
            ]
        ];
        $joins = [
            [
                'table' => 'students',
                'alias' => 'Student',
                'type' => 'INNER',
                'conditions' => [
                    'Student.id = ScoreTotal.student_id'
                ]
            ],
            [
                'table' => 'exams',
                'alias' => 'Exam',
                'type' => 'INNER',
                'conditions' => [
                    'Exam.id = ScoreTotal.exam_id'
                ]
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Student.user_id'
                ]
            ]
        ];
        return Datatable::simple($this, $columns, $joins);
    }

}

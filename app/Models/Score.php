<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
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
 * @method static Builder|Score whereClassRank($value)
 * @method static Builder|Score whereCreatedAt($value)
 * @method static Builder|Score whereEnabled($value)
 * @method static Builder|Score whereExamId($value)
 * @method static Builder|Score whereGradeRank($value)
 * @method static Builder|Score whereId($value)
 * @method static Builder|Score whereScore($value)
 * @method static Builder|Score whereStudentId($value)
 * @method static Builder|Score whereSubjectId($value)
 * @method static Builder|Score whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Exam $exam
 * @property-read \App\Models\Student $student
 * @property-read \App\Models\Subject $subject
 */
class Score extends Model {
    
    protected $fillable = [
        'student_id',
        'subject_id',
        'exam_id',
        'class_rank',
        'grade_rank',
        'score',
        'enabled'
    ];
    
    public function student() {
        return $this->belongsTo('App\Models\Student');
    }
    
    public function subject() {
        return $this->belongsTo('App\Models\Subject');
    }
    
    public function exam() {
        return $this->belongsTo('App\Models\Exam');
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Score.id', 'dt' => 0],
            ['db' => 'Student.student_number', 'dt' => 1],
            ['db' => 'User.realname', 'dt' => 2],
            ['db' => 'Subject.name as subjectname', 'dt' => 3],
            ['db' => 'Exam.name as examname', 'dt' => 4],
            ['db' => 'Score.class_rank', 'dt' => 5,
                'formatter' => function ($d) {
                    return $d === 0 ? "未统计" : $d;
                }
            ],
            ['db' => 'Score.grade_rank', 'dt' => 6,
                'formatter' => function ($d) {
                    return $d === 0 ? "未统计" : $d;
                }
            ],
            ['db' => 'Score.score', 'dt' => 7],
            ['db' => 'Score.created_at', 'dt' => 8],
            ['db' => 'Score.updated_at', 'dt' => 9],
            [
                'db' => 'Score.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'students',
                'alias' => 'Student',
                'type' => 'INNER',
                'conditions' => [
                    'Student.id = Score.student_id'
                ]
            ],
            [
                'table' => 'subjects',
                'alias' => 'Subject',
                'type' => 'INNER',
                'conditions' => [
                    'Subject.id = Score.subject_id'
                ]
            ],
            [
                'table' => 'exams',
                'alias' => 'Exam',
                'type' => 'INNER',
                'conditions' => [
                    'Exam.id = Score.exam_id'
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

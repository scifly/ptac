<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Http\Requests\ScoreRequest;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'student_id', 'subject_id', 'exam_id',
        'class_rank', 'grade_rank', 'score',
        'enabled',
    ];
    
    public function student() { return $this->belongsTo('App\Models\Student'); }
    
    public function subject() { return $this->belongsTo('App\Models\Subject'); }
    
    public function exam() { return $this->belongsTo('App\Models\Exam'); }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'Score.id', 'dt' => 0],
            ['db' => 'Student.student_number', 'dt' => 1],
            ['db' => 'User.realname', 'dt' => 2],
            ['db' => 'Subject.name as subjectname', 'dt' => 3],
            ['db' => 'Exam.name as examname', 'dt' => 4],
            ['db'        => 'Score.class_rank', 'dt' => 5,
             'formatter' => function ($d) {
                 return $d === 0 ? "未统计" : $d;
             },
            ],
            ['db'        => 'Score.grade_rank', 'dt' => 6,
             'formatter' => function ($d) {
                 return $d === 0 ? "未统计" : $d;
             },
            ],
            ['db' => 'Score.score', 'dt' => 7],
            ['db' => 'Score.created_at', 'dt' => 8],
            ['db' => 'Score.updated_at', 'dt' => 9],
            [
                'db'        => 'Score.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'students',
                'alias'      => 'Student',
                'type'       => 'INNER',
                'conditions' => [
                    'Student.id = Score.student_id',
                ],
            ],
            [
                'table'      => 'subjects',
                'alias'      => 'Subject',
                'type'       => 'INNER',
                'conditions' => [
                    'Subject.id = Score.subject_id',
                ],
            ],
            [
                'table'      => 'exams',
                'alias'      => 'Exam',
                'type'       => 'INNER',
                'conditions' => [
                    'Exam.id = Score.exam_id',
                ],
            ],
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Student.user_id',
                ],
            ],
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }
    
    public function statistics($exam_id) {
        $class_ids = DB::table('exams')->where('id', $exam_id)->value('class_ids');
        $class = DB::table('classes')
            ->whereIn('id', explode(',', $class_ids))
            ->select('id', 'grade_id')
            ->get();
        //通过年级分组
        $grades = [];
        foreach ($class as $item) {
            $grades[$item->grade_id][] = $item->id;
        }
        //循环每个年级
        foreach ($grades as $class_ids_arr) {
            //查找此年级所有班级的学生的各科成绩
            $score = $this
                ->join('students', 'students.id', '=', 'scores.student_id')
                ->whereIn('students.class_id', $class_ids_arr)
                ->where('scores.exam_id', $exam_id)
                ->select('scores.id', 'scores.student_id', 'scores.subject_id', 'scores.score', 'students.class_id')
                ->orderBy('scores.score', 'desc')
                ->get();
            //通过科目分组
            $subject = [];
            foreach ($score as $item) {
                $subject[$item->subject_id][] = $item;
            }
            //循环每个科目
            foreach ($subject as $val) {
                foreach ($val as $k => $v) {
                    $v->grade_rank = $k + 1;
                    if ($k > 0) {
                        if ($v->score == $val[$k - 1]->score) {
                            $v->grade_rank = $val[$k - 1]->grade_rank;
                        }
                    }
                }
                //写入年级排名
                foreach ($val as $grade_rank) {
                    $this->where('id', $grade_rank->id)->update(['grade_rank' => $grade_rank->grade_rank]);
                }
                //通过班级分组
                $classes = [];
                foreach ($val as $item) {
                    $classes[$item->class_id][] = $item;
                }
                //循环每个班级
                foreach ($classes as $v) {
                    foreach ($v as $class_k => $class_v) {
                        $class_v->class_rank = $class_k + 1;
                        if ($class_k > 0) {
                            if ($class_v->score == $v[$class_k - 1]->score) {
                                $class_v->class_rank = $v[$class_k - 1]->class_rank;
                            }
                        }
                    }
                    //写入年级排名
                    foreach ($v as $class_rank) {
                        $this->where('id', $class_rank->id)->update(['class_rank' => $class_rank->class_rank]);
                    }
                }
            }
        }
        
        return true;
    }
    
}

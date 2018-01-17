<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * App\Models\Score 分数
 *
 * @property int $id
 * @property int $student_id 学生ID
 * @property int $subject_id 科目ID
 * @property int $exam_id 考试ID
 * @property int $class_rank 班级排名
 * @property int $grade_rank 年级排名
 * @property float $score 分数
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
 * @mixin Eloquent
 * @property-read Exam $exam
 * @property-read Student $student
 * @property-read Subject $subject
 */
class Score extends Model {

    protected $fillable = [
        'student_id', 'subject_id', 'exam_id',
        'class_rank', 'grade_rank', 'score',
        'enabled',
    ];
    
    /**
     * 返回分数记录所属的学生对象
     * 
     * @return BelongsTo
     */
    public function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * 返回分数记录所属的科目对象
     * 
     * @return BelongsTo
     */
    public function subject() { return $this->belongsTo('App\Models\Subject'); }
    
    /**
     * 返回分数记录所述的考试对象
     * 
     * @return BelongsTo
     */
    public function exam() { return $this->belongsTo('App\Models\Exam'); }
    
    /**
     * 分数记录列表
     *
     * @return array
     */
    static function datatable() {

        $columns = [
            ['db' => 'Score.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Grade.name as gradename', 'dt' => 2],
            ['db' => 'Squad.name', 'dt' => 3],
            ['db' => 'Student.student_number', 'dt' => 4],
            ['db' => 'Subject.name as subjectname', 'dt' => 5],
            ['db' => 'Exam.name as examname', 'dt' => 6],
            ['db' => 'Score.class_rank', 'dt' => 7,
                'formatter' => function ($d) {
                    return $d === 0 ? "未统计" : $d;
                },
            ],
            ['db' => 'Score.grade_rank', 'dt' => 8,
                'formatter' => function ($d) {
                    return $d === 0 ? "未统计" : $d;
                },
            ],
            ['db' => 'Score.score', 'dt' => 9],
            ['db' => 'Score.created_at', 'dt' => 10],
            ['db' => 'Score.updated_at', 'dt' => 11],
            [
                'db' => 'Score.enabled', 'dt' => 12,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'students',
                'alias' => 'Student',
                'type' => 'INNER',
                'conditions' => [
                    'Student.id = Score.student_id',
                ],
            ],
            [
                'table' => 'subjects',
                'alias' => 'Subject',
                'type' => 'INNER',
                'conditions' => [
                    'Subject.id = Score.subject_id',
                ],
            ],
            [
                'table' => 'exams',
                'alias' => 'Exam',
                'type' => 'INNER',
                'conditions' => [
                    'Exam.id = Score.exam_id',
                ],
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Student.user_id',
                ],
            ],

            [
                'table' => 'classes',
                'alias' => 'Squad',
                'type' => 'INNER',
                'conditions' => [
                    'Squad.id = Student.class_id',
                ],
            ],
            [
                'table' => 'grades',
                'alias' => 'Grade',
                'type' => 'INNER',
                'conditions' => [
                    'Grade.id = Squad.grade_id',
                ],
            ],
        ];
        // todo: 增加过滤条件
        return Datatable::simple(self::getModel(), $columns, $joins);
        
    }

    static function statistics($exam_id) {
        
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
            $score = self::join('students', 'students.id', '=', 'scores.student_id')
                ->whereIn('students.class_id', $class_ids_arr)
                ->where('scores.exam_id', $exam_id)
                ->select(['scores.id', 'scores.student_id', 'scores.subject_id', 'scores.score', 'students.class_id'])
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
                    self::find($grade_rank->id)->update(['grade_rank' => $grade_rank->grade_rank]);
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
                        self::find($class_rank->id)->update(['class_rank' => $class_rank->class_rank]);
                    }
                }
            }
        }

        return true;
    }
    public function scores($exam, $squad, $subject, $project) {
        $student = $this->where('exam_id', $exam)
            ->get()->pluck('student_id');
        # 当前班级下的所有参加考试的学生
        $students = Student::whereClassId($squad)->whereIn('id', $student)->get();
        # 当前选择班级的所属年级下 的所有班级 id
        $classes = Squad::where('grade_id', Squad::whereId($squad)->first()->grade_id)
            ->get()
            ->pluck('id');
        # 统计当前学生年级 的所有参加考试的学生
        $gradeStudents = Student::whereIn('class_id', $classes)
            ->whereIn('id', $student)
            ->get();
        $result = [];
        foreach ($students as $s) {
            $user = User::whereIn('id', array_column(json_decode($s->custodians), 'user_id'))
                ->get();
            $student = $s->user['realname'];
            $score = $this->where('exam_id', $exam)
                ->where('student_id', $s->id)
                ->get();
            $message = [];

            foreach ($subject as $j) {
                $sub = Subject::whereId($j)->first();
                $subScore = $this::where('exam_id', $exam)
                    ->where('subject_id', $j)
                    ->where('student_id', $s->id)
                    ->first();
                $subName = isset($sub->name) ? $sub->name : '';
                $sum = $score->sum('score');

                foreach ($project as $p) {
                    if ($p == 'score') {
                        if ($j == '-1') {
                            $message[]= '总分:'.$sum;
                        }else{
                            $message[]= $sub->name .':' . $subScore->score;
                        }
                    }
                    if ($p == 'grade_rank') {
                        if ($j == '-1') {
                            $total = ScoreTotal::where('student_id', $s->id)
                                ->where('exam_id', $exam)
                                ->first();
                            $message[]= '年排名:'.$total->grade_rank;
                        }else{
                            $stotal = Score::where('student_id', $s->id)
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->first();
                            $message[]= $subName . '(年排):' . $stotal->grade_rank;
                        }
                    }
                    if ($p == 'class_rank') {
                        if ($j == '-1') {
                            $total = ScoreTotal::where('student_id', $s->id)
                                ->where('exam_id', $exam)
                                ->first();
                            $message[]= '班排名:'.$total->class_rank;
                        }else{
                            $stotal = Score::where('student_id', $s->id)
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->first();
                            $message[]= $subName . '(班排):' . $stotal->class_rank;
                        }
                    }
                    if ($p == 'grade_average') {
                        if ($j == '-1') {
                            $gaToTal = ScoreTotal::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->get();
                            $ga = $gaToTal->sum('score') / $gaToTal->count();
                            $message[]= '年平均:' . $ga;
                        }else{
                            $sgaToTal = Score::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->get();
                            $sga = $sgaToTal->sum('score') / $sgaToTal->count();

                            $message[]= $subName . '(年平均):' . $sga;
                        }
                    }
                    if ($p == 'class_average') {
                        if ($j == '-1') {
                            $caToTal = ScoreTotal::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->get();
                            $ca = $caToTal->sum('score') / $caToTal->count();
                            $message[]= '班平均:' . $ca;
                        }else{
                            $scaToTal = Score::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->get();
                            $sca = $scaToTal->sum('score') / $scaToTal->count();

                            $message[]= $subName . '(班平均):' . $sca;
                        }
                    }
                    if ($p == 'grade_max') {
                        if ($j == '-1') {
                            $maxTotal = ScoreTotal::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->max('score');
                            $message[]= '年最高:' . $maxTotal;
                        }else{
                            $maxSub = Score::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->max('score');
                            $message[]= $subName . '(年最高):' . $maxSub;
                        }
                    }
                    if ($p == 'class_max') {
                        if ($j == '-1') {
                            $cmaxTotal = ScoreTotal::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->max('score');
                            $message[]= '班最高:' . $cmaxTotal;
                        }else{
                            $cmaxSub = Score::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->max('score');
                            $message[]= $subName . '(班最高):' . $cmaxSub;
                        }

                    }
                    if ($p == 'grade_min') {
                        if ($j == '-1') {
                            $minTotal = ScoreTotal::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->min('score');
                            $message[]= '年最低:' . $minTotal;
                        }else{
                            $minSub = Score::whereIn('student_id', $gradeStudents->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->min('score');
                            $message[]= $subName . '(年最低):' . $minSub;
                        }
                    }
                    if ($p == 'class_min') {
                        if ($j == '-1') {
                            $cminTotal = ScoreTotal::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->min('score');
                            $message[]= '班最低:' . $cminTotal;
                        }else{
                            $cminSub = Score::whereIn('student_id', $students->pluck('id'))
                                ->where('exam_id', $exam)
                                ->where('subject_id', $j)
                                ->min('score');
                            $message[]= $subName . '(班最低):' . $cminSub;
                        }
                    }

                }
            }
            $result[] = [
                'custodian' => $user->pluck('realname'),
                'name' => $student,
                'mobile' => Mobile::whereIn('user_id', $user->pluck('id'))->get()->pluck('mobile'),
                'content' => '尊敬的' . $student . '家长,'
                    . Exam::whereId($exam)->first()->name . '考试成绩已出:' . implode(',', $message) . '。'
            ];
            unset($message);


        }

        return $result;

    }


    public function sendMessage($mobile, $content) {

    }
}

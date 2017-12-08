<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

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
 * @property-read Exam $exam
 * @property-read Student $student
 * @property-read Subject $subjects
 * @method static Builder|ScoreTotal whereEnabled($value)
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
        'enabled',
    ];
    
    /**
     * 返回总分记录所属的学生对象
     *
     * @return BelongsTo
     */
    public function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * 返回总分记录所属的考试对象
     *
     * @return BelongsTo
     */
    public function exam() { return $this->belongsTo('App\Models\Exam'); }
    
    /**
     * 返回总分记录所属的科目对象
     *
     * @return BelongsTo
     */
    function subject() { return $this->belongsTo('App\Models\Subject'); }

    public function datatable() {
        
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

                },
            ],
        ];
        $joins = [
            [
                'table' => 'students',
                'alias' => 'Student',
                'type' => 'INNER',
                'conditions' => [
                    'Student.id = ScoreTotal.student_id',
                ],
            ],
            [
                'table' => 'exams',
                'alias' => 'Exam',
                'type' => 'INNER',
                'conditions' => [
                    'Exam.id = ScoreTotal.exam_id',
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
        ];

        return Datatable::simple($this, $columns, $joins);
        
    }

    /**
     * 统计
     *
     * @param $exam_id
     * @return bool
     * @throws Exception
     */
    public function statistics($exam_id) {
        
        //删除之前这场考试的统计
        try {
            $this->where('exam_id', $exam_id)->delete();
        } catch (Exception $e) {
            throw $e;
        }
        //查询参与这场考试的所有班级和科目
        $exam = DB::table('exams')->where('id', $exam_id)->select('class_ids', 'subject_ids')->first();
        $class = DB::table('classes')
            ->whereIn('id', explode(',', $exam->class_ids))
            ->select('id', 'grade_id')
            ->get();
        //通过年级分组
        $grades = [];
        foreach ($class as $item) {
            $grades[$item->grade_id][] = $item->id;
        }
        //循环每个年级
        foreach ($grades as $class_ids_arr) {
            $data = [];
            //查找此年级参与考试班级的所有学生
            $students = DB::table('students')
                ->whereIn('class_id', $class_ids_arr)
                ->pluck('class_id', 'id');
            //循环学生
            foreach ($students as $student => $class_id) {
                //计算总成绩
                $scores = DB::table('scores')
                    ->where(['student_id' => $student, 'exam_id' => $exam_id])
                    ->pluck('score', 'subject_id');
                $score = 0;
                $subject_ids = '';
                $na_subject_ids = '';
                foreach (explode(',', $exam->subject_ids) as $v) {
                    if (isset($scores[$v]) && $scores[$v] != 0) {
                        $subject_ids .= ',' . $v;
                        $score += $scores[$v];
                    } else {
                        $na_subject_ids .= ',' . $v;
                    }
                }
                //建立写入数据库的数组数据
                $insert = [
                    'student_id' => $student,
                    'class_id' => $class_id,
                    'exam_id' => intval($exam_id),
                    'score' => $score,
                    'subject_ids' => empty($subject_ids) ? '' : substr($subject_ids, 1),
                    'na_subject_ids' => empty($na_subject_ids) ? '' : substr($na_subject_ids, 1),
                ];
                $data [] = $insert;
            }
            //根据总成绩排序
            $score_sore = [];
            foreach ($data as $key => $row) {
                $score_sore[$key] = $row['score'];
            }
            array_multisort($score_sore, SORT_DESC, $data);
            //计算年级排名
            $grade_ranks = [];
            foreach ($data as $grade_k => $grade_v) {
                $grade_v['grade_rank'] = $grade_k + 1;
                if ($grade_k > 0) {
                    if ($grade_v['score'] == $data[$grade_k - 1]['score']) {
                        $grade_v['grade_rank'] = $grade_ranks[0]['grade_rank'];
                    }
                }
                $grade_ranks [] = $grade_v;
            }
            //通过班级分组
            $classes = [];
            foreach ($grade_ranks as $item) {
                $classes[$item['class_id']][] = $item;
            }
            //循环每个班级
            foreach ($classes as $v) {
                //计算班级排名
                $inserts = [];
                foreach ($v as $class_k => $class_v) {
                    $class_v['class_rank'] = $class_k + 1;
                    if ($class_k > 0) {
                        if ($class_v['score'] == $v[$class_k - 1]['score']) {
                            $class_v['class_rank'] = $inserts[$class_k - 1]['class_rank'];
                        }
                    }
                    unset($class_v['class_id']);
                    $inserts [] = $class_v;
                }
                $this->insert($inserts);
            }
        }

        return true;
        
    }
    
}

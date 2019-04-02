<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use ReflectionException;
use Throwable;

/**
 * App\Models\ScoreTotal 总分
 *
 * @property int $id
 * @property int $student_id 学生ID
 * @property int $exam_id 考试ID
 * @property float $score 总分
 * @property string $subject_ids 计入总成绩的科目IDs
 * @property string $na_subject_ids 未计入总成绩的科目IDs
 * @property int $class_rank 班级排名
 * @property int $grade_rank 年级排名
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Exam $exam
 * @property-read Student $student
 * @property-read Subject $subject
 * @method static Builder|ScoreTotal whereClassRank($value)
 * @method static Builder|ScoreTotal whereCreatedAt($value)
 * @method static Builder|ScoreTotal whereEnabled($value)
 * @method static Builder|ScoreTotal whereExamId($value)
 * @method static Builder|ScoreTotal whereGradeRank($value)
 * @method static Builder|ScoreTotal whereId($value)
 * @method static Builder|ScoreTotal whereNaSubjectIds($value)
 * @method static Builder|ScoreTotal whereScore($value)
 * @method static Builder|ScoreTotal whereStudentId($value)
 * @method static Builder|ScoreTotal whereSubjectIds($value)
 * @method static Builder|ScoreTotal whereUpdatedAt($value)
 * @method static Builder|ScoreTotal newModelQuery()
 * @method static Builder|ScoreTotal newQuery()
 * @method static Builder|ScoreTotal query()
 * @mixin Eloquent
 */
class ScoreTotal extends Model {
    
    use ModelTrait;
    
    protected $table = 'score_totals';
    protected $fillable = [
        'student_id', 'exam_id', 'score',
        'subject_ids', 'na_subject_ids', 'class_rank',
        'grade_rank', 'enabled',
    ];
    
    /**
     * 返回总分记录所属的学生对象
     *
     * @return BelongsTo
     */
    function student() { return $this->belongsTo('App\Models\Student'); }
    
    /**
     * 返回总分记录所属的考试对象
     *
     * @return BelongsTo
     */
    function exam() { return $this->belongsTo('App\Models\Exam'); }
    
    /**
     * 返回指定总分记录包含的计入或未计入总分的考试科目
     *
     * @param $id
     * @return array
     */
    function subjects($id) {
        
        $st = $this->find($id);
        abort_if(!$st, HttpStatusCode::NOT_FOUND, __('messages.not_found'));
        
        return array_map(
            function ($field) use ($st) {
                return Subject::whereIn('id', explode(',', $st->{$field}))->get();
            },
            ['subject_ids', 'na_subject_ids']
        );
        
    }
    
    /**
     * 总成绩记录列表
     *
     * @return array
     * @throws ReflectionException
     */
    function index() {
        
        $columns = [
            ['db' => 'ScoreTotal.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Student.sn', 'dt' => 2],
            [
                'db'        => 'Grade.name as gradename', 'dt' => 3,
                'formatter' => function ($d) {
                    return Snippet::icon($d, 'grade');
                },
            ],
            [
                'db'        => 'Squad.name', 'dt' => 4,
                'formatter' => function ($d) {
                    return Snippet::icon($d, 'squad');
                },
            ],
            ['db' => 'Exam.name as examname', 'dt' => 5],
            ['db' => 'ScoreTotal.score', 'dt' => 6],
            ['db' => 'ScoreTotal.grade_rank', 'dt' => 7],
            ['db' => 'ScoreTotal.class_rank', 'dt' => 8],
            ['db' => 'ScoreTotal.created_at', 'dt' => 9, 'dr' => true],
            ['db' => 'ScoreTotal.updated_at', 'dt' => 10, 'dr' => true],
            [
                'db'        => 'ScoreTotal.enabled', 'dt' => 11,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false, false, true);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'students',
                'alias'      => 'Student',
                'type'       => 'INNER',
                'conditions' => [
                    'Student.id = ScoreTotal.student_id',
                ],
            ],
            [
                'table'      => 'exams',
                'alias'      => 'Exam',
                'type'       => 'INNER',
                'conditions' => [
                    'Exam.id = ScoreTotal.exam_id',
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
            [
                'table'      => 'classes',
                'alias'      => 'Squad',
                'type'       => 'INNER',
                'conditions' => [
                    'Squad.id = Student.class_id',
                ],
            ],
            [
                'table'      => 'grades',
                'alias'      => 'Grade',
                'type'       => 'INNER',
                'conditions' => [
                    'Grade.id = Squad.grade_id',
                ],
            ],
        ];
        $condition = 'Student.id IN (' . implode(',', $this->contactIds('student')) . ')';
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存总成绩
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新总成绩
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * （批量）删除总成绩
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge(['ScoreTotal'], 'id', 'purge', $id);
        
    }
    
    /**
     * 统计
     *
     * @param $examId
     * @return bool
     * @throws Exception
     */
    function stat($examId) {
        
        abort_if(
            !Exam::find($examId),
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $exam = Exam::find($examId);
        $role = Auth::user()->role();
        if ($role != '运营') {
            # 对当前用户可见的学生Id
            $allowedStudentIds = $this->contactIds('student');
            # 参与指定考试的学生Id
            $examStudentIds = self::whereExamId($examId)->pluck('student_id')->toArray();
            /**
             * 如果指定考试所属学校对当前用户不可见，或者指定考试包含的
             * 部分或所有学生对当前用户不可见，则抛出403异常
             */
            abort_if(
                !in_array($exam->examType->school_id, $this->schoolIds())
                || !empty(array_diff($allowedStudentIds, $examStudentIds)),
                HttpStatusCode::FORBIDDEN,
                __('messages.forbidden')
            );
        }
        // 删除之前这场考试的统计
        $this->where('exam_id', $examId)->delete();
        // 查询参与这场考试的所有班级和科目
        $exam = Exam::find($examId)->get(['class_ids', 'subject_ids'])->first();
        $classes = Squad::whereIn('id', explode(',', $exam->class_ids))->get(['id', 'grade_id']);
        //通过年级分组
        $grades = [];
        foreach ($classes as $class) {
            $grades[$class->grade_id][] = $class->id;
        }
        //循环每个年级
        foreach ($grades as $classIds) {
            $data = [];
            //查找此年级参与考试班级的所有学生
            $students = Student::whereIn('class_id', $classIds)->pluck('class_id', 'id');
            //循环学生
            foreach ($students as $studentId => $class_id) {
                //计算总成绩
                $scores = Score::where(['student_id' => $studentId, 'exam_id' => $examId])
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
                    'student_id'     => $studentId,
                    'class_id'       => $class_id,
                    'exam_id'        => intval($examId),
                    'score'          => $score,
                    'subject_ids'    => empty($subject_ids) ? '' : substr($subject_ids, 1),
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
            foreach ($grade_ranks as $class) {
                $classes[$class['class_id']][] = $class;
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

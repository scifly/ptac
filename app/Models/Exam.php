<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\{Constant, ModelTrait};
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\Facades\{Auth, Request};
use Throwable;

/**
 * App\Models\Exam 考试
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read ExamType $examType
 * @property-read Collection|Score[] $score
 * @property-read Collection|Score[] $scores
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
 * @method static Builder|Exam newModelQuery()
 * @method static Builder|Exam newQuery()
 * @method static Builder|Exam query()
 * @mixin Eloquent
 * @property-read int|null $scores_count
 */
class Exam extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'exam_type_id', 'class_ids', 'subject_ids', 'name',  
        'max_scores', 'pass_scores', 'start_date', 'end_date',
        'remark', 'enabled',
    ];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function examType() { return $this->belongsTo('App\Models\ExamType'); }
    
    /** @return HasMany */
    function scores() { return $this->hasMany('App\Models\Score'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 考试列表
     *
     * @return array
     * @throws Exception
     */
    function index() {
        
        $columns = [
            ['db' => 'Exam.id', 'dt' => 0],
            ['db' => 'Exam.name', 'dt' => 1],
            ['db' => 'ExamType.name as examtypename', 'dt' => 2],
            ['db' => 'Exam.max_scores', 'dt' => 3],
            ['db' => 'Exam.pass_scores', 'dt' => 4],
            ['db' => 'Exam.start_date', 'dt' => 5, 'dr' => true],
            ['db' => 'Exam.end_date', 'dt' => 6, 'dr' => true],
            ['db' => 'Exam.created_at', 'dt' => 7, 'dr' => true],
            ['db' => 'Exam.updated_at', 'dt' => 8, 'dr' => true],
            [
                'db'        => 'Exam.enabled', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'exam_types',
                'alias'      => 'ExamType',
                'type'       => 'INNER',
                'conditions' => [
                    'ExamType.id = Exam.exam_type_id',
                ],
            ],
        ];
        $condition = 'ExamType.school_id = ' . $this->schoolId();
        if (!in_array(Auth::user()->role(), Constant::SUPER_ROLES)) {
            $condition .= ' AND class_ids IN (' . $this->classIds()->join(',') . ')';
        }
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存考试
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新考试
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id = null) {
        
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 删除考试
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id, [
            'purge.exam_id' => ['Score', 'ScoreTotal']
        ]);
        
    }
    
    /** Helper functions --------------------------------------------------------------------------------------------
     * @throws Exception
     */
    function compose() {
        
        $action = explode('/', Request::path())[1];
        $schoolId = $this->schoolId();
        if ($action == 'index') {
            $nil = collect([null => '全部']);
            $htmlExamType = $this->htmlSelect(
                $nil->union(ExamType::whereSchoolId($schoolId)->pluck('name', 'id')),
                'filter_exam_type'
            );
            $data = [
                'titles' => [
                    '#', '名称',
                    ['title' => '类型', 'html' => $htmlExamType],
                    '满分', '及格分数',
                    [
                        'title' => '开始日期',
                        'html'  => $this->htmlDTRange('开始日期', false),
                    ],
                    [
                        'title' => '结束日期',
                        'html'  => $this->htmlDTRange('结束日期', false),
                    ],
                    [
                        'title' => '创建于',
                        'html'  => $this->htmlDTRange('创建于'),
                    ],
                    [
                        'title' => '更新于',
                        'html'  => $this->htmlDTRange('更新于'),
                    ],
                    [
                        'title' => '状态 . 操作',
                        'html'  => $this->htmlSelect(
                            $nil->union(['已禁用', '已启用']), 'filter_enabled'
                        ),
                    ],
                ],
                'batch'  => true,
                'filter' => true,
            ];
        } else {
            $where = ['enabled' => 1];
            $classes = Squad::whereIn('id', $this->classIds())->where($where);
            $where['school_id'] = $schoolId;
            $examtypes = ExamType::where($where);
            $gradeIds = $classes->pluck('grade_id')->unique();
            $subjects = Subject::where($where)->get()->filter(
                function (Subject $subject) use ($gradeIds) {
                    $subjectGradeIds = explode(',', $subject->grade_ids);
                    return $gradeIds->intersect($subjectGradeIds)->isNotEmpty();
                }
            );
            $exam = Exam::find(Request::route('id'));
            $data = array_merge(
                array_combine(
                    ['classes', 'examtypes', 'subjects'],
                    array_map(
                        function ($records) {
                            return $records->{'pluck'}('name', 'id');
                        }, [$classes, $examtypes, $subjects]
                    )
                ),
                array_combine(
                    ['selectedClasses', 'selectedSubjects'],
                    array_map(
                        function ($class, $field) use ($exam) {
                            if (!$exam) return null;
                            $where = 'id IN (' . ($exam->{$field} ?? '') . ')';
                            return $this->model($class)->whereRaw($where)->pluck('id');
                        }, ['Squad', 'Subject'], ['class_ids', 'subject_ids']
                    )
                )
            );
        }
        
        return $data;
        
    }
    
    /**
     * 获取指定班级的所有考试
     *
     * @param $classId
     * @param null $keyword
     * @return array
     * @throws Throwable
     */
    function examsByClassId($classId, $keyword = null) {
        
        abort_if(
            !$classId,
            Constant::NOT_ACCEPTABLE,
            __('messages.score.zero_classes')
        );
        $exams = $this->whereRaw('FIND_IN_SET(' . $classId . ', class_ids)')->get();
        $filtered = $exams->reject(function (Exam $exam) use ($keyword) {
            return $keyword
                ? mb_strpos($exam->name, $keyword) === false ? true : false
                : false;
        });
        
        return $filtered->toArray();
        
    }
    
    /**
     * 获取指定教职员工所在班级的所有考试及分数
     *
     * @return array|bool
     * @throws Exception
     */
    function examsByEducator() {
        
        $scores = $classNames = $classIds = [];
        $classes = Squad::whereEnabled(Constant::ENABLED)->whereIn('id', $this->classIds())->get();
        foreach ($classes as $k => $c) {
            $exams = Exam::whereEnabled(Constant::ENABLED)->get();
            foreach ($exams as $key => $e) {
                if (in_array($c->id, explode(',', $e->class_ids))) {
                    $scores[$k][$key]['id'] = $e->id;
                    $scores[$k][$key]['name'] = $e->name;
                    $scores[$k][$key]['classname'] = $c->name;
                    $scores[$k][$key]['start_date'] = $e->start_date;
                    $scores[$k][$key]['class_id'] = $c->id;
                    $scores[$k][$key]['subject_ids'] = $e->subject_ids;
                }
            }
            $classNames[] = [
                'title' => $c->name,
                'value' => $c->id,
            ];
        }
        
        return [
            'score'     => $scores,
            'className' => $classNames,
        ];
        
    }
    
    /**
     * 返回指定考试对应的班级列表html
     *
     * @param $id
     * @param $action
     * @return mixed
     */
    function classList($id, $action = null) {
        
        $exam = $this->find($id);
        if (!$exam) {
            $classes = [];
        } else {
            $classes = Squad::whereIn('id', explode(',', $exam->class_ids))
                ->where('enabled', 1)
                ->pluck('name', 'id')
                ->toArray();
        }
        
        return response()->json([
            'html' => $this->htmlSelect($classes, $action ? $action . '_class_id' : 'class_id'),
        ]);
        
    }
    
}

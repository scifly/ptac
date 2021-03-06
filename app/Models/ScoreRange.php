<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Doctrine\Common\Inflector\Inflector;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\{DB, Request};
use Throwable;

/**
 * App\Models\ScoreRange 分数统计范围
 *
 * @property int $id
 * @property string $name 成绩统计项名称
 * @property string $subject_ids 成绩统计项包含的科目IDs
 * @property int $school_id 成绩统计项所属学校ID
 * @property float $start_score 成绩统计项起始分数
 * @property float $end_score 成绩统计项截止分数
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled 是否统计
 * @property-read School $school
 * @method static Builder|ScoreRange whereCreatedAt($value)
 * @method static Builder|ScoreRange whereEnabled($value)
 * @method static Builder|ScoreRange whereEndScore($value)
 * @method static Builder|ScoreRange whereId($value)
 * @method static Builder|ScoreRange whereName($value)
 * @method static Builder|ScoreRange whereSchoolId($value)
 * @method static Builder|ScoreRange whereStartScore($value)
 * @method static Builder|ScoreRange whereSubjectIds($value)
 * @method static Builder|ScoreRange whereUpdatedAt($value)
 * @method static Builder|ScoreRange newModelQuery()
 * @method static Builder|ScoreRange newQuery()
 * @method static Builder|ScoreRange query()
 * @mixin Eloquent
 */
class ScoreRange extends Model {
    
    use ModelTrait;
    
    protected $table = 'score_ranges';
    
    protected $fillable = [
        'name', 'subject_ids', 'school_id',
        'start_score', 'end_score', 'created_at',
        'updated_at', 'enabled',
    ];
    
    /** Properties -------------------------------------------------------------------------------------------------- */
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** crud -------------------------------------------------------------------------------------------------------- */
    /**
     * 分数统计范围列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'ScoreRange.id', 'dt' => 0],
            ['db' => 'ScoreRange.name', 'dt' => 1],
            ['db' => 'ScoreRange.start_score', 'dt' => 2],
            ['db' => 'ScoreRange.end_score', 'dt' => 3],
            ['db' => 'ScoreRange.created_at', 'dt' => 4],
            ['db' => 'ScoreRange.updated_at', 'dt' => 5],
            [
                'db'        => 'ScoreRange.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $condition = 'ScoreRange.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, null, $condition
        );
        
    }
    
    /**
     * 保存成绩统计项
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新成绩统计项
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
    
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 删除成绩统计项
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
    /**
     * 按分数范围进行统计
     *
     * @return JsonResponse
     */
    function stat() {
        
        $request = Request::all();
        //查询班级
        if ($request['type'] == 'grade') {
            $classes = DB::table('classes')
                ->where('grade_id', $request['grade_id'])
                ->select('id', 'grade_id')
                ->pluck('id');
        } else {
            $classes = [$request['class_id']];
        }
        //查找符合条件的所有成绩
        $score = DB::table('scores')
            ->join('students', 'students.id', '=', 'scores.student_id')
            ->whereIn('students.class_id', $classes)
            ->where('scores.exam_id', $request['exam_id'])
            ->select('scores.id', 'scores.student_id', 'scores.subject_id', 'scores.score')
            ->orderBy('scores.score', 'desc')
            ->get();
        //查找所有成绩统计项
        $score_range = self::all()->toArray();
        //循环成绩项
        foreach ($score_range as $v) {
            $v->number = 0;
            //统计项统计科目
            $subject_ids = explode(',', $v->subject_ids);
            //计算学生这些科目总成绩
            $item = [];
            foreach ($score as $val) {
                //判断该科成绩是否需要统计
                if (in_array($val->subject_id, $subject_ids)) {
                    if (!isset($item[$val->student_id])) {
                        $item[$val->student_id] = $val->score;
                    } else {
                        $item[$val->student_id] += $val->score;
                    }
                }
            }
            //若成绩在统计项范围内统计数量加一
            foreach ($item as $val) {
                if ($v->end_score > $val && $val >= $v->start_score) {
                    $v->number++;
                }
            }
            if (count($item) != 0) {
                $v->precentage = round($v->number / count($item) * 100, 2);
            }
            
        }
        
        return response()->json($score_range);
        
    }
    
    /** Helper functions -------------------------------------------------------------------------------------------- */
    /**
     * 返回composer所需的view数据
     *
     * @return array
     */
    function compose() {
        
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                $data = [
                    'buttons' => [
                        'stat' => [
                            'id'    => 'stat',
                            'label' => '统计',
                            'icon'  => 'fa fa-bar-chart',
                        ],
                    ],
                    'titles'  => ['#', '名称', '起始分数', '截止分数', '创建于', '更新于', '状态 . 操作'],
                ];
                break;
            case 'stat':
                $data = array_combine(
                    ['grades', 'classes', 'exams'],
                    array_map(
                        function ($class) {
                            $model = $this->model($class);
                            $field = ($class == 'Grade' ? 'grade' : 'class') . '_ids';
                            $method = Inflector::camelize($field);
                            $builder = $model->whereEnabled(1);
                            $builder = $class == 'Exam'
                                ? $builder->{'whereRaw'}($field . ' IN(' . join(',', $this->{$method}()) . ')')
                                : $builder->{'whereIn'}('id', $this->{$method}());
                            
                            return $builder->{'pluck'}('name', 'id');
                        }, ['Grade', 'Squad', 'Exam']
                    )
                );
                break;
            default:
                $sr = ScoreRange::find(Request::route('id'));
                $subjects = Subject::where(['school_id' => $this->schoolId(), 'enabled' => 1])
                    ->pluck('name', 'id');
                $data = [
                    'subjects'         => $subjects->prepend('总分'),
                    'selectedSubjects' => collect($sr ? explode(',', $sr->subject_ids) : []),
                ];
                break;
        }
        
        return $data;
        
    }
}

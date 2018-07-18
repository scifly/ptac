<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
 * @mixin Eloquent
 * @property-read ExamType $examType
 * @property-read Collection|Score[] $score
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Score[] $scores
 */
class Exam extends Model {
    
    use ModelTrait;
    
    protected $table = 'exams';
    
    protected $fillable = [
        'name', 'remark', 'exam_type_id',
        'class_ids', 'subject_ids', 'max_scores',
        'pass_scores', 'start_date', 'end_date',
        'enabled',
    ];
    
    /**
     * 返回指定考试所属的考试类型对象
     *
     * @return BelongsTo
     */
    function examType() { return $this->belongsTo('App\models\ExamType'); }
    
    /**
     * 返回考试
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    function scores() { return $this->hasMany('App\Models\Score'); }
    
    /**
     * 考试列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Exam.id', 'dt' => 0],
            ['db' => 'Exam.name', 'dt' => 1],
            ['db' => 'Exam.remark', 'dt' => 2],
            ['db' => 'ExamType.name as examtypename', 'dt' => 3],
            ['db' => 'Exam.max_scores', 'dt' => 4],
            ['db' => 'Exam.pass_scores', 'dt' => 5],
            ['db' => 'Exam.start_date', 'dt' => 6],
            ['db' => 'Exam.end_date', 'dt' => 7],
            ['db' => 'Exam.created_at', 'dt' => 8],
            ['db' => 'Exam.updated_at', 'dt' => 9],
            [
                'db'        => 'Exam.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
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
        if (!in_array(Auth::user()->group->name, Constant::SUPER_ROLES)) {
            $condition .= ' AND class_ids IN (' . implode(',', $this->classIds()) . ')';
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
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
     * @throws Exception
     */
    function modify(array $data, $id = null) {
        
        return $id
            ? $this->find($id)->update($data)
            : $this->batch($this);
        
    }
    
    /**
     * 删除考试
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 从所有考试中删除指定的科目
     *
     * @param $subjectId
     * @throws Exception
     */
    function removeSubject($subjectId) {
        
        try {
            DB::transaction(function () use ($subjectId) {
                $exams = $this->whereRaw($subjectId . ' IN (subject_ids)')->get();
                foreach ($exams as $exam) {
                    $subjectIds = array_diff(explode(',', $exam->subject_ids), [$subjectId]);
                    $exam->update(['subject_ids' => implode(',', $subjectIds)]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
    /**
     * 删除指定考试的所有相关数据
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                Score::whereExamId($id)->delete();
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
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
            HttpStatusCode::NOT_ACCEPTABLE,
            __('messages.score.zero_classes')
        );

        $exams = $this->whereRaw('FIND_IN_SET(' . $classId . ', class_ids)')->get();
        
        $filtered = $exams->reject(function (Exam $exam) use ($keyword) {
            return $keyword ? mb_strpos($exam->name, $keyword)  : false;
            

        });
        
        return $filtered->toArray();
        
    }
    
    /**
     * 获取指定教职员工所在班级的所有考试及分数
     *
     * @return array|bool
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
                ->whereEnabled(1)
                ->pluck('name', 'id')
                ->toArray();
        }
        
        return response()->json([
            'html' => $this->singleSelectList($classes, $action ? $action . '_class_id' : 'class_id'),
        ]);
        
    }
    
}

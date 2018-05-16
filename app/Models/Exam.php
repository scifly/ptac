<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use ReflectionException;

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
    function score() { return $this->hasMany('App\Models\Score'); }
    
    /**
     * 获取参与指定考试的所有班级列表
     *
     * @param $classIds
     * @return array
     */
    function selectedClasses($classIds) {
        
        $classIds = explode(",", $classIds);
        $selectedClasses = [];
        foreach ($classIds as $classId) {
            $class = Squad::find($classId);
            $selectedClasses[$classId] = $class['name'];
        }
        
        return $selectedClasses;

    }

    /**
     * 获取指定考试包含的所有科目列表
     *
     * @param $subjectIds
     * @return array
     */
    function selectedSubjects($subjectIds = null) {

        $subjectIds = explode(",", $subjectIds);
        $selectedSubjects = [];
        foreach ($subjectIds as $subjectId) {
            $selectedSubjects[$subjectId] = Subject::find($subjectId)->name;
        }
        return $selectedSubjects;

    }
    

    /**
     * 保存考试
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        $exam = self::create($data);

        return $exam ? true : false;

    }

    /**
     * 更新考试
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $exam = self::find($id);
        if (!$exam) { return false; }

        return $exam->update($data);

    }
    
    /**
     * 删除考试
     *
     * @param $id
     * @return bool|null
     * @throws ReflectionException
     * @throws Exception
     */
    function remove($id) {
        
        $exam = $this->find($id);
        if (!$exam) { return false; }
        
        return $this->removable($exam) ? $exam->delete() : false;
        
    }
    
    /**
     * 获取指定班级的所有考试
     *
     * @param $classId
     * @param null $keyword
     * @return array
     */
    function examsByClassId($classId, $keyword = null) {
        
        $values = [];
        $exams = $this->when(
            $keyword,
            function (Exam $query) use ($keyword) {
                return $query->where('name', 'like', '%' . $keyword . '%');
            }
        )->whereRaw('FIND_IN_SET(' . $classId . ', class_ids)')->get();
        
        foreach ($exams as $key => $e) {
            $values[$key]['id'] = $e->id;
            $values[$key]['name'] = $e->name;
            $values[$key]['start_date'] = $e->start_date;
            $values[$key]['class_id'] = $classId;
            $values[$key]['subject_ids'] = $e->subject_ids;
        }
        
        return $values;
        
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
            'html' => $this->singleSelectList($classes, $action ? $action . '_class_id' : 'class_id')
        ]);
        
    }
    
    /**
     * 考试列表
     *
     * @return array
     */
    function datatable() {
        
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
                'db' => 'Exam.enabled', 'dt' => 10,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'exam_types',
                'alias' => 'ExamType',
                'type' => 'INNER',
                'conditions' => [
                    'ExamType.id = Exam.exam_type_id',
                ],
            ],
        ];
        $condition = 'ExamType.school_id = ' . $this->schoolId();
        if (!in_array(Auth::user()->group->name, Constant::SUPER_ROLES)) {
            $condition .= ' AND class_ids IN (' . $this->implode(',', $this->classIds()) . ')';
        }
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );

    }

}

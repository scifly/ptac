<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
    public function examType() { return $this->belongsTo('App\models\ExamType'); }

    /**
     * 返回考试
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function score() { return $this->hasMany('App\Models\Score'); }
    /**
     * 获取参与指定考试的所有班级列表
     *
     * @param $classIds
     * @return array
     */
    static function classes($classIds) {
        
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
    static function subjects($subjectIds = null) {

        $subjectIds = explode(",", $subjectIds);
        $selectedSubjects = [];
        foreach ($subjectIds as $subjectId) {
            $selectedSubjects[$subjectId] = Subject::find($subjectId)->name;
        }
        return $selectedSubjects;

    }
    
    /**
     * 获取当前考试班级
     *
     * @param $classIds
     * @return array
     */
    static function examClasses($classIds) {
        
        $class_ids = explode(',', $classIds);
        $classes = [];
        foreach ($class_ids as $class_id) {
            $classes[] = Squad::find($class_id);
        }

        return $classes;

    }

    /**
     * 返回班级相关的所有考试
     *
     * @param $classId
     * @return array
     */
    static function examsByClassId($classId) {
        
        $exams = self::all();
        $_exams = [];
        foreach ($exams as $exam) {
            $classIds = explode(',', $exam->class_ids);
            if (in_array($classId, $classIds)) {
                $_exams[] = $exam;
            }
        }
        
        return $_exams;

    }

    /**
     * 获取指定考试包含的的科目列表
     *
     * @param $id
     * @return array
     * @internal param $subjectIds
     */
    static function subjectsByExamId($id) {
        
        $subjectIds = self::find($id)->first(["subject_ids"])->toArray();
        $subject_ids = explode(',', $subjectIds['subject_ids']);
        $subjects = [];
        foreach ($subject_ids as $subject_id) {
            $subjects[] = Subject::whereId($subject_id)->first(['id', 'name']);
        }
        
        return $subjects;

    }

    /**
     * 保存考试
     *
     * @param array $data
     * @return bool
     */
    static function store(array $data) {
        
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
    static function modify(array $data, $id) {
        
        $exam = self::find($id);
        if (!$exam) { return false; }

        return $exam->update($data) ? true : false;

    }

    /**
     * 删除考试
     *
     * @param $id
     * @return bool
     */
    static function remove($id) {
        
        $exam = self::find($id);
        if (!$exam) { return false; }

        return $exam->removable($exam) ? true : false;

    }
    
    /**
     * 考试列表
     *
     * @return array
     */
    static function datatable() {
        
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
        // todo: 增加角色过滤条件
        $condition = 'ExamType.school_id = ' . School::schoolId();
        
        return Datatable::simple(self::getModel(), $columns, $joins, $condition);

    }

}

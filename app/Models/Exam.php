<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Exam
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
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
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
 * @mixin \Eloquent
 * @property-read \App\Models\ExamType $examType
 */
class Exam extends Model {
    
    protected $table = 'exams';
    
    protected $fillable = [
        'name',
        'remark',
        'exam_type_id',
        'class_ids',
        'subject_ids',
        'max_scores',
        'pass_scores',
        'start_date',
        'end_date',
        'created_at',
        'updated_at',
        'enabled'
    ];
    
    public function examType() {
        
        return $this->belongsTo('App\models\ExamType');
        
    }
    
    public function classes(array $classIds) {
        
        return Squad::whereIn('id', $classIds)->get(['id', 'name']);
        
    }
    
    public function subjects(array $subjectIds) {
        
        return Subject::whereIn('id', $subjectIds)->get(['id', 'name']);
        
    }
    
    //获取当前考试班级
    public function examClasses($classIds) {
        
        $class_ids = explode(',', $classIds);
        $classes = [];
        foreach ($class_ids as $class_id) {
            $classes[] = Squad::whereId($class_id)->first();
        }
        return $classes;
        
    }
    
    /**
     * 返回班级相关的所有考试
     *
     * @param $class_id
     * @return array
     */
    public function examsByClassId($class_id) {
        
        $exams = $this::all();
        $_exams = [];
        foreach ($exams as $exam) {
            $classIds = explode(',', $exam->class_ids);
            if (in_array($class_id, $classIds)) {
                $_exams[] = $exam;
            }
        }
        
        return $_exams;
        
    }
    
    
    /**
     * 获取当前考试的科目
     * @param $examId
     * @return array
     * @internal param $subjectIds
     */
    public function subjectsByExamId($examId) {
        
        
        $subjectIds = self::whereid($examId)->first(["subject_ids"])->toArray();
        $subject_ids = explode(',', $subjectIds['subject_ids']);
        $subjects = [];
        foreach ($subject_ids as $subject_id) {
            $subjects[] = Subject::whereId($subject_id)->first(['id', 'name']);
        }
        return $subjects;
    }
    
    
    public function datatable() {
        
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
                    return Datatable::dtOps($this, $d, $row);
                }
            ]
        ];
        $joins = [
            [
                'table' => 'exam_types',
                'alias' => 'ExamType',
                'type' => 'INNER',
                'conditions' => [
                    'ExamType.id = Exam.exam_type_id'
                ]
            
            ]
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }
    
}

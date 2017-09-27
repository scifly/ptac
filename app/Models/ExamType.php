<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ExamType 考试类型
 *
 * @property int $id
 * @property string $name 考试类型名称
 * @property string $remark 考试类型备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ExamType whereCreatedAt($value)
 * @method static Builder|ExamType whereEnabled($value)
 * @method static Builder|ExamType whereId($value)
 * @method static Builder|ExamType whereName($value)
 * @method static Builder|ExamType whereRemark($value)
 * @method static Builder|ExamType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Exam $Exam
 * @property-read Exam[] $exams
 * @property-read \App\Models\School $school
 */
class ExamType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'remark', 'school_id', 'enabled'];
    
    /**
     * 返回所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定考试类型包含的所有考试对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function exams() { return $this->hasMany('App\Models\Exam'); }
    
    /**
     * 保存考试类型
     *
     * @param array $data
     * @return bool
     */
    public function store(array $data) {
        
        $examType = $this->create($data);
        return $examType ? true : false;
        
    }
    
    /**
     * 更新考试类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    public function modify(array $data, $id) {
        
        $examType = $this->find($id);
        if (!$examType) {
            return false;
        }
        return $examType->update($data) ? true : false;
        
    }
    
    /**
     * 删除考试类型
     *
     * @param $id
     * @return bool
     */
    public function remove($id) {
        
        $examType = $this->find($id);
        if (!$examType) { return false; }
        return $this->removable($examType) ? $examType->delete() : false;
        
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'ExamType.id', 'dt' => 0],
            ['db' => 'ExamType.name', 'dt' => 1],
            ['db' => 'School.name as schoolname', 'dt' => 2],
            ['db' => 'ExamType.remark', 'dt' => 3],
            ['db' => 'ExamType.created_at', 'dt' => 4],
            ['db' => 'ExamType.updated_at', 'dt' => 5],
            [
                'db'        => 'ExamType.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = ExamType.school_id',
                ],
            ],
        ];
        
        return Datatable::simple($this, $columns, $joins);
    }
    
}

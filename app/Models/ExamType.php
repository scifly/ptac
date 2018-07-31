<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\ExamType 考试类型
 *
 * @property int $id
 * @property int $school_id
 * @property string $name 考试类型名称
 * @property string $remark 考试类型备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Exam[] $exams
 * @property-read School $school
 * @method static Builder|ExamType whereCreatedAt($value)
 * @method static Builder|ExamType whereEnabled($value)
 * @method static Builder|ExamType whereId($value)
 * @method static Builder|ExamType whereName($value)
 * @method static Builder|ExamType whereRemark($value)
 * @method static Builder|ExamType whereSchoolId($value)
 * @method static Builder|ExamType whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ExamType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'remark', 'school_id', 'enabled'];
    
    /**
     * 返回所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 获取指定考试类型包含的所有考试对象
     *
     * @return HasMany
     */
    function exams() { return $this->hasMany('App\Models\Exam'); }
    
    /**
     * 考试类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'ExamType.id', 'dt' => 0],
            ['db' => 'ExamType.name', 'dt' => 1],
            ['db' => 'ExamType.remark', 'dt' => 2],
            ['db' => 'ExamType.created_at', 'dt' => 3],
            ['db' => 'ExamType.updated_at', 'dt' => 4],
            [
                'db'        => 'ExamType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
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
        $condition = 'ExamType.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存考试类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新考试类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除考试类型
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定考试类型的所有数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->delRelated('exam_type_id', 'Exam', $id);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}

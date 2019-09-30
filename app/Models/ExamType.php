<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
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
 * @method static Builder|ExamType newModelQuery()
 * @method static Builder|ExamType newQuery()
 * @method static Builder|ExamType query()
 * @mixin Eloquent
 * @property-read int|null $exams_count
 */
class ExamType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['school_id', 'name', 'remark', 'enabled'];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return HasMany */
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
                    return Datatable::status($d, $row, false);
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
            $this, $columns, $joins, $condition
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
     * @throws Throwable
     */
    function modify(array $data, $id) {
    
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * 删除考试类型
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id, [
            'purge.exam_type_id' => ['Exam']
        ]);
        
    }
    
}

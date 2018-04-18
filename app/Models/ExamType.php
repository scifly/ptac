<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * 保存考试类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        $et = self::create($data);

        return $et ? true : false;

    }

    /**
     * 更新考试类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        $et = self::find($id);
        if (!$et) { return false; }

        return $et->update($data) ? true : false;

    }
    
    /**
     * 删除考试类型
     *
     * @param $id
     * @return bool
     * @throws Exception
     */
    function remove($id) {
        
        $et = self::find($id);
        if (!$et) { return false; }

        return self::removable($et) ? $et->delete() : false;

    }
    
    /**
     * 考试类型列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'ExamType.id', 'dt' => 0],
            ['db' => 'ExamType.name', 'dt' => 1],
            ['db' => 'ExamType.remark', 'dt' => 2],
            ['db' => 'ExamType.created_at', 'dt' => 3],
            ['db' => 'ExamType.updated_at', 'dt' => 4],
            [
                'db' => 'ExamType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
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

}

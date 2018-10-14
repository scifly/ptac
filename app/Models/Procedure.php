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
 * App\Models\Procedure 审批流程
 *
 * @property int $id
 * @property int $procedure_type_id 流程类型ID
 * @property int $school_id 流程所属学校ID
 * @property string $name 流程名称
 * @property string $remark 流程备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|Procedure whereCreatedAt($value)
 * @method static Builder|Procedure whereEnabled($value)
 * @method static Builder|Procedure whereId($value)
 * @method static Builder|Procedure whereName($value)
 * @method static Builder|Procedure whereProcedureTypeId($value)
 * @method static Builder|Procedure whereRemark($value)
 * @method static Builder|Procedure whereSchoolId($value)
 * @method static Builder|Procedure whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read ProcedureType $procedureType
 * @property-read School $school
 * @property-read Collection|ProcedureLog[] $procedureLogs
 * @property-read Collection|ProcedureStep[] $procedureSteps
 */
class Procedure extends Model {
    
    use ModelTrait;
    
    protected $table = 'procedures';
    
    protected $fillable = [
        'procedure_type_id', 'school_id', 'name',
        'remark', 'enabled',
    ];
    
    /**
     * 返回指定流程所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /**
     * 返回指定流程所属的流程类型对象
     *
     * @return BelongsTo
     */
    function procedureType() { return $this->belongsTo('App\Models\ProcedureType'); }
    
    /**
     * 获取指定审批流程包含的所有审批流程步骤对象
     *
     * @return HasMany
     */
    function procedureSteps() { return $this->hasMany('App\Models\ProcedureStep'); }
    
    /**
     * 获取指定审批流程包含的所有流程审批日志对象
     *
     * @return HasMany
     */
    function procedureLogs() { return $this->hasMany('App\Models\ProcedureLog'); }
    
    /**
     * 审批流程列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Procedures.id', 'dt' => 0],
            ['db' => 'ProcedureType.name as proceduretypename', 'dt' => 1],
            ['db' => 'Procedures.name', 'dt' => 2],
            ['db' => 'Procedures.remark', 'dt' => 3],
            ['db' => 'Procedures.created_at', 'dt' => 4],
            ['db' => 'Procedures.updated_at', 'dt' => 5],
            [
                'db'        => 'Procedures.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'procedure_types',
                'alias'      => 'ProcedureType',
                'type'       => 'INNER',
                'conditions' => [
                    'ProcedureType.id = Procedures.procedure_type_id',
                ],
            ],
        ];
        $condition = 'School.id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存审批流程
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新审批流程
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id = null) {
        
        return $id
            ? $this->find($id)->update($data)
            : $this->batch($this);
        
    }
    
    /**
     * 删除审批流程
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->del($this, $id);
        
    }
    
    /**
     * 删除指定审批流程的所有先关数据
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function purge($id) {
        
        try {
            DB::transaction(function () use ($id) {
                $classes = ['ProcedureStep', 'ProcedureLog'];
                $keys = array_fill(0, sizeof($classes), $id);
                $values = array_fill(0, sizeof($classes), $id);
                array_map([$this, 'delRelated'], $keys, $classes, $values);
                $this->find($id)->delete();
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}


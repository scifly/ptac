<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Exception;
use Illuminate\Database\Eloquent\{Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\Facades\{DB, Request};
use Throwable;

/**
 * App\Models\FlowType 审批流程
 *
 * @property int $id
 * @property int $school_id 所属学校id
 * @property string $name 流程名称
 * @property mixed $steps 流程步骤
 * @property string $remark 流程备注
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $enabled
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Flow[] $flows
 * @property-read int|null $flows_count
 * @property-read \App\Models\School $school
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType whereRemark($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType whereSchoolId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType whereSteps($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\FlowType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class FlowType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['school_id', 'name', 'steps', 'remark', 'enabled'];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return HasMany */
    function flows() { return $this->hasMany('App\Models\Flow'); }
    
    /**
     * 审批流程列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'FlowType.id', 'dt' => 0],
            ['db' => 'FlowType.name', 'dt' => 1],
            ['db' => 'FlowType.remark', 'dt' => 2],
            ['db' => 'FlowType.created_at', 'dt' => 3],
            ['db' => 'FlowType.updated_at', 'dt' => 4],
            [
                'db'        => 'FlowType.enabled', 'dt' => 5,
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
                    'School.id = FlowType.school_id',
                ],
            ],
        ];
        $condition = 'School.id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
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
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                Request::replace(['ids' => $ids]);
                $this->purge(['FlowType', 'Flow'], 'flow_type_id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}


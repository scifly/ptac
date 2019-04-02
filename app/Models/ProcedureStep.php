<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

/**
 * App\Models\ProcedureStep 审批流程步骤
 *
 * @property int $id
 * @property int $procedure_id 流程ID
 * @property string $name 流程步骤名称
 * @property string $approver_user_ids 审批人用户IDs
 * @property string $related_user_ids 相关人用户IDs
 * @property string $remark 流程步骤备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Procedure $procedure
 * @method static Builder|ProcedureStep whereApproverUserIds($value)
 * @method static Builder|ProcedureStep whereCreatedAt($value)
 * @method static Builder|ProcedureStep whereEnabled($value)
 * @method static Builder|ProcedureStep whereId($value)
 * @method static Builder|ProcedureStep whereName($value)
 * @method static Builder|ProcedureStep whereProcedureId($value)
 * @method static Builder|ProcedureStep whereRelatedUserIds($value)
 * @method static Builder|ProcedureStep whereRemark($value)
 * @method static Builder|ProcedureStep whereUpdatedAt($value)
 * @method static Builder|ProcedureStep newModelQuery()
 * @method static Builder|ProcedureStep newQuery()
 * @method static Builder|ProcedureStep query()
 * @mixin Eloquent
 */
class ProcedureStep extends Model {
    
    // todo: needs to be refactored
    use ModelTrait;
    
    protected $table = 'procedure_steps';
    
    protected $fillable = [
        'procedure_id', 'name', 'approver_user_ids',
        'related_user_ids', 'remark', 'enabled',
    ];
    
    /**
     * 返回指定审批流程步骤所属的审批流程对象
     *
     * @return BelongsTo
     */
    function procedure() { return $this->belongsTo('App\Models\Procedure'); }
    
    /**
     * 审批流程步骤列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'ProcedureStep.id', 'dt' => 0],
            ['db' => 'Procedures.name as procedurename', 'dt' => 1],
            [
                'db'        => 'ProcedureStep.approver_user_ids', 'dt' => 2,
                'formatter' => function ($d) {
                    return $this->userList($d);
                },
            ],
            [
                'db'        => 'ProcedureStep.related_user_ids', 'dt' => 3,
                'formatter' => function ($d) {
                    return $this->userList($d);
                },
            ],
            ['db' => 'ProcedureStep.name', 'dt' => 4],
            ['db' => 'ProcedureStep.remark', 'dt' => 5],
            ['db' => 'ProcedureStep.created_at', 'dt' => 6],
            ['db' => 'ProcedureStep.updated_at', 'dt' => 7],
            [
                'db'        => 'ProcedureStep.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'procedures',
                'alias'      => 'Procedures',
                'type'       => 'INNER',
                'conditions' => [
                    'Procedures.id = ProcedureStep.procedure_id',
                ],
            ],
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
    /**
     * 保存审批流程步骤
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新审批流程步骤
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
     * 删除审批流程步骤
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge(['ProcedureStep'], 'id', 'purge', $id);
        
    }
    
    /**
     * 返回指定审批流程步骤相关的审批者用户
     *
     * @param string $d
     * @return string
     */
    private function userList($d) {
        
        $userList = User::whereIn('id', explode(',', $d))
            ->pluck('realname')->toArray();
        
        return implode(',', $userList);
        
    }
    
}

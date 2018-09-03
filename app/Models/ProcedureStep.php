<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
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
                'formatter' => function ($row) {
                    return $this->approverUsers($row['id']);
                },
            ],
            [
                'db'        => 'ProcedureStep.related_user_ids', 'dt' => 3,
                'formatter' => function ($row) {
                    return $this->relatedUsers($row['id']);
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
        
        return Datatable::simple($this->getModel(), $columns, $joins);
        
    }
    
    /**
     * 返回指定审批流程步骤相关的审批者用户
     *
     * @param $id
     * @return string
     */
    private function approverUsers($id) {
        
        return self::users($id, 'approver_user_ids');
        
    }
    
    /**
     * 根据流程步骤ID获取审批者/相关人用户列表
     *
     * @param $id integer 流程步骤ID
     * @param $field string (用户ID)字段名称
     * @return string
     */
    private function users($id, $field) {
        
        $userIds = Auth::user()->userList(explode(',', $this->find($id)->{$field}));
        $userList = collect($userIds)->flatten()->toArray();
        
        return implode(',', $userList);
        
    }
    
    /**
     * 返回相关人用户列表
     *
     * @param $id
     * @return string
     */
    private function relatedUsers($id) {
        
        return $this->users($id, 'related_user_ids');
        
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
    
    /** Helper functions -------------------------------------------------------------------------------------------- */

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
     * @throws Exception
     */
    function remove($id = null) {
        
        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
        
    }
    
    /**
     * 从审批流程步骤中删除指定的用户
     *
     * @param $userId
     * @throws Throwable
     */
    function removeUser($userId) {
        
        try {
            DB::transaction(function () use ($userId) {
                $condition = $userId . ' IN (approver_user_ids) OR ' . $userId . ' IN (related_user_ids)';
                $pses = $this->whereRaw($condition)->get();
                foreach ($pses as $ps) {
                    $user_ids = array_map(function ($field) use ($ps, $userId) {
                        return implode(',', array_diff(explode(',', $ps->{$field}), [$userId]));
                    }, ['approver_user_ids', 'related_user_ids']);
                    $ps->update([
                        'approver_user_ids' => $user_ids[0],
                        'related_user_ids'  => $user_ids[1],
                    ]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}

<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ProcedureStep
 *
 * @property int $id
 * @property int $procedure_id 流程ID
 * @property string $name 流程步骤名称
 * @property string $approver_user_ids 审批人用户IDs
 * @property string $related_user_ids 相关人用户IDs
 * @property string $remark 流程步骤备注
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ProcedureStep whereApproverUserIds($value)
 * @method static Builder|ProcedureStep whereCreatedAt($value)
 * @method static Builder|ProcedureStep whereEnabled($value)
 * @method static Builder|ProcedureStep whereId($value)
 * @method static Builder|ProcedureStep whereName($value)
 * @method static Builder|ProcedureStep whereProcedureId($value)
 * @method static Builder|ProcedureStep whereRelatedUserIds($value)
 * @method static Builder|ProcedureStep whereRemark($value)
 * @method static Builder|ProcedureStep whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read Procedure $procedure
 */
class ProcedureStep extends Model {

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
    public function procedure() { return $this->belongsTo('App\Models\Procedure'); }

    /**
     * 保存审批流程步骤
     *
     * @param array $data
     * @return bool
     */
    static function store(array $data) {

        $ps = self::create($data);

        return $ps ? true : false;

    }

    /**
     * 更新审批流程步骤
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    static function modify(array $data, $id) {

        $p = self::find($id);
        if (!$p) { return false; }

        return $p->update($data) ? true : false;

    }
    
    /**
     * 删除审批流程步骤
     *
     * @param $id
     * @return bool|null
     * @throws Exception
     */
    static function remove($id) {

        $p = self::find($id);
        if (!$p) { return false; }
        
        return $p->removable($p) ? $p->delete() : false;

    }
    
    /**
     * 审批流程步骤列表
     *
     * @return array
     */
    static function datatable() {

        $columns = [
            ['db' => 'ProcedureStep.id', 'dt' => 0],
            ['db' => 'Procedures.name as procedurename', 'dt' => 1],
            [
                'db' => 'ProcedureStep.approver_user_ids', 'dt' => 2,
                'formatter' => function ($row) {
                    return self::approverUsers($row['id']);
                },
            ],
            [
                'db' => 'ProcedureStep.related_user_ids', 'dt' => 3,
                'formatter' => function ($row) {
                    return self::relatedUsers($row['id']);
                },
            ],
            ['db' => 'ProcedureStep.name', 'dt' => 4],
            ['db' => 'ProcedureStep.remark', 'dt' => 5],
            ['db' => 'ProcedureStep.created_at', 'dt' => 6],
            ['db' => 'ProcedureStep.updated_at', 'dt' => 7],
            [
                'db' => 'ProcedureStep.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'procedures',
                'alias' => 'Procedures',
                'type' => 'INNER',
                'conditions' => [
                    'Procedures.id = ProcedureStep.procedure_id',
                ],
            ],
        ];

        return Datatable::simple(self::getModel(), $columns, $joins);
        
    }

    /**
     * 返回审批者用户列表
     *
     * @param $id
     * @return string
     */
    private static function approverUsers($id) {

        return self::userList($id, 'approver_user_ids');

    }

    /**
     * 根据流程步骤ID获取审批者/相关人用户列表
     *
     * @param $id integer 流程步骤ID
     * @param $field string (用户ID)字段名称
     * @return string
     */
    private static function userList($id, $field) {

        $ps = self::find($id);
        $userIds = User::users(explode(',', $ps->{$field}));
        $userList = collect($userIds)->flatten()->toArray();

        return implode(',', $userList);

    }

    /**
     * 返回相关人用户列表
     *
     * @param $id
     * @return string
     */
    private static function relatedUsers($id) {

        return self::userList($id, 'related_user_ids');

    }

}

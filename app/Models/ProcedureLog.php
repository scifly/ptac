<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Facades\DatatableFacade as Datatable;

/**
 * App\Models\ProcedureLog
 *
 * @property int $id
 * @property int $initiator_user_id 发起人用户ID
 * @property int $procedure_id 流程ID
 * @property int $procedure_step_id 流程步骤ID
 * @property int $operator_user_id 操作者用户ID
 * @property string $initiator_msg （发起人）步骤相关留言
 * @property string $initiator_media_ids （发起人）步骤相关附件媒体IDs
 * @property string $operator_msg （操作者）步骤相关留言
 * @property string $operator_media_ids （操作者）步骤相关附件媒体IDs
 * @property int $step_status 步骤状态：0-通过、1-拒绝、2-待定
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static Builder|ProcedureLog whereCreatedAt($value)
 * @method static Builder|ProcedureLog whereId($value)
 * @method static Builder|ProcedureLog whereInitiatorMediaIds($value)
 * @method static Builder|ProcedureLog whereInitiatorMsg($value)
 * @method static Builder|ProcedureLog whereInitiatorUserId($value)
 * @method static Builder|ProcedureLog whereOperatorMediaIds($value)
 * @method static Builder|ProcedureLog whereOperatorMsg($value)
 * @method static Builder|ProcedureLog whereOperatorUserId($value)
 * @method static Builder|ProcedureLog whereProcedureId($value)
 * @method static Builder|ProcedureLog whereProcedureStepId($value)
 * @method static Builder|ProcedureLog whereStepStatus($value)
 * @method static Builder|ProcedureLog whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProcedureLog extends Model {
    //
    protected $table = 'procedure_logs';

    protected $fillable = [
        'initiator_user_id',
        'procedure_id',
        'procedure_step_id',
        'operator_user_id',
        'initiator_msg',
        'operator_media_ids',
        'step_status',
        'created_at',
        'updated_at',
    ];

    /**
     * 流程日志与用户
     */
    public function user() {
        return $this->belongsTo('App\Models\User');
    }

    /**
     * 流程日志与流程
     */
    public function procedure() {
        return $this->belongsTo('App\Models\Procedure');
    }

    /**
     * 日志与流程步骤
     */
    public function procedureStep() {
        return $this->belongsTo('App\Models\ProcedureStep');
    }

    /**
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */
    public function users($userId){
        return User::find($userId);
    }

    public function status($d){

        switch ($d){
            case 0: return '通过';
            case 1: return '拒绝';
            case 2: return '待定';
            default: return 'N/A';
        }
    }

    public function datatable(){

        $columns = [
            ['db' => 'ProcedureLog.id', 'dt' => 0],
            [
                'db' => 'ProcedureLog.initiator_user_id', 'dt' => 1,
                'formatter' => function($d, $row) {
                       $user = $this->users($d);
                       return $user->realname;
                }
            ],
            ['db' => 'Procedures.name as procedurename', 'dt' => 2],
            ['db' => 'ProcedureStep.name procedurestepname', 'dt' => 3],
            [
                'db' => 'ProcedureLog.operator_user_id', 'dt' => 4,
                'formatter' => function($d, $row) {
                    $user = $this->users($d);
                    return $user->realname;
                }
            ],
            ['db' => 'ProcedureLog.initiator_msg', 'dt' => 5],
            ['db' => 'ProcedureLog.operator_msg', 'dt' => 6],
            ['db' => 'ProcedureLog.created_at', 'dt' => 7],
            ['db' => 'ProcedureLog.updated_at', 'dt' => 8],
            [
                'db' => 'ProcedureLog.step_status', 'dt' => 9,
                'formatter' => function($d, $row) {
                    return $this->status($d);
//                    return Datatable::dtOps($this, $d, $row);
                }
            ],
        ];

        $joins = [
            [
                'table' => 'procedures',
                'alias' => 'Procedures',
                'type' => 'INNER',
                'conditions' => [
                    'Procedures.id = ProcedureLog.procedure_id'
                ]
            ],
            [
                'table' => 'procedure_steps',
                'alias' => 'ProcedureStep',
                'type' => 'INNER',
                'conditions' => [
                    'ProcedureStep.id = ProcedureLog.procedure_step_id'
                ]
            ]
        ];

        return Datatable::simple($this, $columns, $joins);
    }

}

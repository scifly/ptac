<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

/**
 * App\Models\ProcedureLog 审批流程日志
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
 * @property int $first_log_id 该申请第一条记录的id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $initiatorUser
 * @property-read User $operatorUser
 * @property-read Procedure $procedure
 * @property-read ProcedureStep $procedureStep
 * @method static Builder|ProcedureLog whereCreatedAt($value)
 * @method static Builder|ProcedureLog whereFirstLogId($value)
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
 * @mixin Eloquent
 */
class ProcedureLog extends Model {
    
    // todo: needs to be refactored
    const DT_PEND = '<span class="badge bg-orange">%s</span>';
    const JOINS = [
        [
            'table'      => 'procedures',
            'alias'      => 'Procedures',
            'type'       => 'INNER',
            'conditions' => [
                'Procedures.id = ProcedureLog.procedure_id',
            ],
        ],
        [
            'table'      => 'procedure_steps',
            'alias'      => 'ProcedureStep',
            'type'       => 'INNER',
            'conditions' => [
                'ProcedureStep.id = ProcedureLog.procedure_step_id',
            ],
        ],
    ];
    protected $table = 'procedure_logs';
    protected $fillable = [
        'initiator_user_id',
        'procedure_id',
        'procedure_step_id',
        'operator_user_id',
        'initiator_msg',
        'initiator_media_ids',
        'operator_msg',
        'operator_media_ids',
        'step_status',
        'created_at',
        'updated_at',
    ];
    
    /**
     * 返回审批流程日志发起者对应的用户对象
     *
     * @return BelongsTo
     */
    function initiator() { return $this->belongsTo('App\Models\User', 'initiator_user_id'); }
    
    /**
     * 返回审批流程日志审批者对应的用户对象
     *
     * @return BelongsTo
     */
    function operator() { return $this->belongsTo('App\Models\User', 'operator_user_id'); }
    
    /**
     * 返回指定流程日志所属的流程对象
     *
     * @return BelongsTo
     */
    function procedure() { return $this->belongsTo('App\Models\Procedure'); }
    
    /**
     * 返回指定流程日志所属的流程步骤对象
     *
     * @return BelongsTo
     */
    function procedureStep() {
        
        return $this->belongsTo('App\Models\ProcedureStep', 'procedure_step_id');
        
    }
    
    /**
     * 返回指定审批流程日志对应的所有发起者/审批者创建的媒体对象
     *
     * @param $id
     * @return array
     */
    function medias($id) {
        
        return array_map(
            function ($field) use ($id) {
                return Media::whereIn('id', $this->find($id)->{$field})->get();
            }, ['initiator_media_ids', 'operator_media_ids']
        );
        
    }
    
    /**
     * 审批流程日志列表
     *
     * @param $where
     * @return array
     */
    function index($where) {
        
        $columns = [
            ['db' => 'ProcedureLog.first_log_id', 'dt' => 0],
            [
                'db'        => 'ProcedureLog.initiator_user_id', 'dt' => 1,
                'formatter' => function ($d) {
                    return User::find($d)->realname;
                },
            ],
            ['db' => 'Procedures.name as procedure_name', 'dt' => 2],
            ['db' => 'ProcedureStep.name procedure_step_name', 'dt' => 3],
            ['db' => 'ProcedureLog.initiator_msg', 'dt' => 4],
            ['db' => 'ProcedureLog.updated_at', 'dt' => 5],
            [
                'db'        => 'ProcedureLog.step_status', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    switch ($d) {
                        case 0:
                            $status = Snippet::status(true);
                            break;
                        case 1:
                            $status = Snippet::status(false);
                            break;
                        case 2:
                            $status = sprintf(self::DT_PEND, '待定');
                            break;
                        default:
                            $status = Snippet::status(false);
                            break;
                    }
                    $id = $row['first_log_id'];
                    $showLink = sprintf(Snippet::DT_LINK_SHOW, $id);
                    
                    return $status . $showLink;
                },
            ],
        ];
        
        return Datatable::simple($this->getModel(), $columns, self::JOINS, $where);
    }
    
    /**
     * 保存审批流程日志
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新审批流程日志
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除审批流程日志
     *
     * @param null $id
     * @return bool|null
     * @throws \Exception
     */
    function remove($id = null) {
        
        return $id
            ? $this->find($id)->delete()
            : $this->whereIn('id', array_values(Request::input('ids')))->delete();
        
    }
    
    /**
     * 从审批流程日志中删除用户数据
     *
     * @param $userId
     * @throws Exception
     */
    function removeUser($userId) {
        
        try {
            DB::transaction(function () use ($userId) {
                $this->where('initiator_user_id', $userId)->delete();
                $this->where('operator_user_id', $userId)->update(['operator_user_id' => 0]);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}

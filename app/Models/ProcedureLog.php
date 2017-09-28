<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
 * @property-read Procedure $procedure
 * @property-read ProcedureStep $procedureStep
 * @property-read User $user
 * @property int $first_log_id 该申请第一条记录的id
 * @property-read User $initiator_user
 * @property-read User $operator_user
 * @property-read ProcedureStep $procedure_step
 * @method static Builder|ProcedureLog whereFirstLogId($value)
 */
class ProcedureLog extends Model {
    
    const DT_PEND = '<span class="badge bg-orange">%s</span>';
    
    protected $table = 'procedure_logs';
    
    protected $joins = [
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
     * 返回审批流程发起者对应的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function initiatorUser() {
        
        return $this->belongsTo('App\Models\User', 'initiator_user_id');
        
    }
    
    /**
     * 返回审批流程操作者对应的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function operatorUser() {
        return $this->belongsTo('App\Models\User', 'operator_user_id');
    }
    
    /**
     * 返回指定流程日志所属的流程对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function procedure() {
        return $this->belongsTo('App\Models\Procedure');
    }
    
    /**
     * 返回指定流程日志所属的流程步骤对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function procedureStep() {
        return $this->belongsTo('App\Models\ProcedureStep', 'procedure_step_id');
    }
    
    /**
     * 拆分initiator_media_ids、operator_media_ids,
     * @param $media_ids
     * @return array 处理后字典 key=>media.id,value => media
     */
    public function operate_ids($media_ids) {
        
        $ids = explode(',', $media_ids);
        $medias = [];
        foreach ($ids as $mid) {
            $media = Media::find($mid);
            $medias[$mid] = $media;
        }
        return $medias;
    }
    
    public function datatable($where) {
        
        $columns = [
            ['db' => 'ProcedureLog.first_log_id', 'dt' => 0],
            [
                'db'        => 'ProcedureLog.initiator_user_id', 'dt' => 1,
                'formatter' => function ($d) {
                    return $this->get_user($d)->realname;
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
                            $status = sprintf(Datatable::DT_ON, '通过');
                            break;
                        case 1:
                            $status = sprintf(Datatable::DT_OFF, '拒绝');
                            break;
                        case 2:
                            $status = sprintf(self::DT_PEND, '待定');
                            break;
                        default:
                            $status = sprintf(Datatable::DT_OFF, '错误');
                            break;
                    }
                    $id = $row['first_log_id'];
                    $showLink = '<a id = ' . $id . ' href="show/' . $id . '" class="btn btn-primary btn-icon btn-circle btn-xs" data-toggle="modal"><i class="fa fa-eye"></i></a>';
                    return $status . Datatable::DT_SPACE . $showLink;
                    
                },
            ],
        ];
        return Datatable::simple($this, $columns, $this->joins, $where);
    }
    
    /**
     * 获取用户信息
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */
    public function get_user($userId) {
        return User::find($userId);
    }
    
}

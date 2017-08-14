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
 * @property-read \App\Models\Procedure $procedure
 * @property-read \App\Models\ProcedureStep $procedureStep
 * @property-read \App\Models\User $user
 */
class ProcedureLog extends Model {
    
    const DT_PEND = '<span class="badge bg-orange">%s</span>';
    
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
     * 步骤状态处理，0-通过，1-拒绝，2-待定
     * @param $d
     * @return string
     */
    public function status($d) {
        
        switch ($d) {
            case 0:
                return '通过';
            case 1:
                return '拒绝';
            case 2:
                return '待定';
            default:
                return '错误';
        }
    }
    
    /**
     * 拆分initiator_media_ids、operator_media_ids,
     * @param $media_ids
     * @return array 处理后字典 key=>media.id,value => media
     */
    public function operate_ids($media_ids) {
        
        $ids = explode(',', $media_ids);
        
        $medias = array();
        foreach ($ids as $mid) {
            $media = Media::find($mid);
            $medias[$mid] = $media;
        }
        
        return $medias;
    }
    
    public function datatable() {
        
        $columns = [
            ['db' => 'ProcedureLog.id', 'dt' => 0],
            [
                'db' => 'ProcedureLog.initiator_user_id', 'dt' => 1,
                'formatter' => function ($d, $row) {
                    $user = $this->get_user($d);
                    return $user->realname;
                }
            ],
            ['db' => 'Procedures.name as procedurename', 'dt' => 2],
            ['db' => 'ProcedureStep.name procedurestepname', 'dt' => 3],
            [
                'db' => 'ProcedureLog.operator_user_id', 'dt' => 4,
                'formatter' => function ($d, $row) {
                    $user = $this->get_user($d);
                    return $user->realname;
                }
            ],
            ['db' => 'ProcedureLog.initiator_msg', 'dt' => 5],
            ['db' => 'ProcedureLog.operator_msg', 'dt' => 6],
            ['db' => 'ProcedureLog.created_at', 'dt' => 7],
            ['db' => 'ProcedureLog.updated_at', 'dt' => 8],
            [
                'db' => 'ProcedureLog.step_status', 'dt' => 9,
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
                            $status = sprintf(Datatable::DT_ON, '通过');
                            break;
                    }
                    
                    $id = $row['id'];
                    $showLink = sprintf(Datatable::DT_LINK_SHOW, /*$model->getTable(),*/
                        $id);
                    $delLink = sprintf(Datatable::DT_LINK_DEL, $id);
                    
                    return $status . Datatable::DT_SPACE . $showLink . Datatable::DT_SPACE . $delLink;
                    
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
    
    /**
     * 获取用户信息
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Collection|Model|null|static|static[]
     */
    public function get_user($userId) {
        return User::find($userId);
    }
    
}

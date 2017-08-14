<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EducatorAppeal
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property string $ea_ids 考勤记录IDs
 * @property string $appeal_content 申诉内容(考勤/会议/其他)
 * @property int $procedure_log_id 相关流程日志ID
 * @property string $approver_educator_ids 审批人教职员工IDs
 * @property string $related_educator_ids 相关人教职员工IDs
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $status 审批状态 0 - 通过 1 - 拒绝 2 - 待审
 * @method static Builder|EducatorAppeal whereAppealContent($value)
 * @method static Builder|EducatorAppeal whereApproverEducatorIds($value)
 * @method static Builder|EducatorAppeal whereCreatedAt($value)
 * @method static Builder|EducatorAppeal whereEaIds($value)
 * @method static Builder|EducatorAppeal whereEducatorId($value)
 * @method static Builder|EducatorAppeal whereId($value)
 * @method static Builder|EducatorAppeal whereProcedureLogId($value)
 * @method static Builder|EducatorAppeal whereRelatedEducatorIds($value)
 * @method static Builder|EducatorAppeal whereStatus($value)
 * @method static Builder|EducatorAppeal whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\Educator $educator
 * @property-read \App\Models\Educator $educatorAttendance
 * @property-read \App\Models\ProcedureLog $procedureLog
 */
class EducatorAppeal extends Model {
    //
    protected $table = 'educator_appeals';
    protected $fillable = [
        'educator_id',
        'ea_ids',
        'appeal_content',
        'procedure_log_id',
        'approver_educator_ids',
        'reated_educator_ids',
        'status'
    ];
    
    /**
     * 教职工申诉与教职工
     */
    public function educator() {
        return $this->belongsTo('App\Models\Educator');
    }
    
    /**
     * 教职工申诉与考勤记录
     */
    public function educatorAttendance() {
        return $this->belongsTo('App\Models\Educator', 'ea_ids');
    }
    
    /**
     * 教职工申诉与流程日志
     */
    public function procedureLog() {
        return $this->belongsTo('App\Models\ProcedureLog', 'procedure_log_id');
    }
    
    public function datatable() {
        $columns = [
            ['db' => 'EducatorAppeal.id', 'dt' => 0],
            ['db' => 'Educator.name as educatorname', 'dt' => 1],
            ['db' => 'EducatorAppeal.appeal_content', 'dt' => 3],
            ['db' => 'ProcedureLog.id', 'dt' => 4],
            ['db' => 'EducatorAppeal.created_at', 'dt' => 7],
            ['db' => 'EducatorAppeal.updated_at', 'dt' => 8],
            [
                'db' => 'EducatorAppeal.status', 'dt' => 9,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($this, $d, $row);
                }
            ],
        ];
        
        $joins = [
            [
                'table' => 'educators',
                'alias' => 'Educator',
                'type' => 'INNER',
                'conditions' => [
                    'Educator.id = EducatorAppeal.educator_id'
                ]
            ]
        ];
        return Datatable::simple($this, $columns, $joins);
    }
    
    
}

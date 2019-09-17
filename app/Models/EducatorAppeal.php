<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Throwable;

/**
 * App\Models\EducatorAppeal 教职员工申诉
 *
 * @property int $id
 * @property int $educator_id 教职员工ID
 * @property string $ea_ids 考勤记录IDs
 * @property string $appeal_content 申诉内容(考勤/会议/其他)
 * @property int $procedure_log_id 相关流程日志ID
 * @property string $approver_educator_ids 审批人教职员工IDs
 * @property string $related_educator_ids 相关人教职员工IDs
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $status 审批状态 0 - 通过 1 - 拒绝 2 - 待审
 * @property-read Educator $educator
 * @property-read Flow $procedureLog
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
 * @method static Builder|EducatorAppeal newModelQuery()
 * @method static Builder|EducatorAppeal newQuery()
 * @method static Builder|EducatorAppeal query()
 * @mixin Eloquent
 */
class EducatorAppeal extends Model {
    
    use ModelTrait;
    
    protected $table = 'educator_appeals';
    
    protected $fillable = [
        'educator_id', 'ea_ids', 'appeal_content',
        'procedure_log_id', 'approver_educator_ids',
        'reated_educator_ids', 'status',
    ];
    
    /**
     * 获取对应的教职员工对象
     *
     * @return BelongsTo
     */
    function educator() { return $this->belongsTo('App\Models\Educator'); }
    
    /**
     * 获取对应的流程日志对象
     *
     * @return BelongsTo
     */
    function procedureLog() { return $this->belongsTo('App\Models\Flow'); }
    
    /**
     * 教职员工申诉记录列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'EducatorAppeal.id', 'dt' => 0],
            ['db' => 'Educator.name as educatorname', 'dt' => 1],
            ['db' => 'EducatorAppeal.appeal_content', 'dt' => 2],
            ['db' => 'Flow.id', 'dt' => 3],
            ['db' => 'EducatorAppeal.created_at', 'dt' => 4],
            ['db' => 'EducatorAppeal.updated_at', 'dt' => 5],
            [
                'db'        => 'EducatorAppeal.status', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'educators',
                'alias'      => 'Educator',
                'type'       => 'INNER',
                'conditions' => [
                    'Educator.id = EducatorAppeal.educator_id',
                ],
            ],
        ];
        
        // todo: 根据学校和角色显示教职员工申诉记录
        return Datatable::simple($this, $columns, $joins);
        
    }
    
    /**
     * 保存申诉
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新申诉
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除申诉
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge([class_basename($this)], 'id', 'purge', $id);
        
    }
    
}
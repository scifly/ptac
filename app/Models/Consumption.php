<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Consumption 消费记录
 *
 * @property int $id
 * @property int $student_id 学生id
 * @property string|null $location 消费地点
 * @property string|null $machineid 消费机id
 * @property int $ctype 消费类型
 * @property float $amount 消费金额
 * @property string $ctime 消费时间
 * @property string $merchant 消费内容
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \App\Models\Student $student
 * @method static Builder|Consumption whereAmount($value)
 * @method static Builder|Consumption whereCreatedAt($value)
 * @method static Builder|Consumption whereCtime($value)
 * @method static Builder|Consumption whereCtype($value)
 * @method static Builder|Consumption whereId($value)
 * @method static Builder|Consumption whereLocation($value)
 * @method static Builder|Consumption whereMachineid($value)
 * @method static Builder|Consumption whereMerchant($value)
 * @method static Builder|Consumption whereStudentId($value)
 * @method static Builder|Consumption whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Consumption extends Model {
    
    use ModelTrait;
    
    protected $table = 'consumptions';
    
    protected $fillable = [
        'student_id', 'location', 'machineid',
        'ctype', 'amount', 'ctime', 'merchant'
    ];
    
    /**
     * 返回消费记录所属的学生对象
     *
     * @return BelongsTo
     */
    function student() {
        
        return $this->belongsTo('App\Models\Student');
        
    }
    
    /**
     * 保存消费记录
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
    
        return $this->create($data) ? true : false;
    
    }
    
    /**
     * 统计
     */
    function stat() {
    
    
    
    }
    
    /**
     * 批量导出
     */
    function export() {
    
    
    
    }
    
    /**
     * 消费记录列表
     *
     * @return array
     */
    function datatable() {
    
        $columns = [
            ['db' => 'Consumption.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Consumption.location', 'dt' => 2],
            ['db' => 'Consumption.machineid', 'dt' => 3],
            [
                'db' => 'Consumption.ctype', 'dt' => 4,
                'formatter' => function ($d) {
                    return $d == 0
                        ? sprintf(Snippet::BADGE_GREEN, '充值')
                        : sprintf(Snippet::BADGE_RED, '消费');
                }
            ],
            [
                'db' => 'Consumption.amount', 'dt' => 5,
                'formatter' => function ($d) {
                    setlocale(LC_MONETARY, 'zh_CN.UTF-8');
                    return money_format('%.2n', $d);
                }
            ],
            ['db' => 'Consumption.ctime', 'dt' => 6]
        ];
        $joins = [
            [
                'table' => 'students',
                'alias' => 'Student',
                'type' => 'INNER',
                'conditions' => [
                    'Student.id = Consumption.student_id'
                ]
            ],
            [
                'table' => 'users',
                'alias' => 'User',
                'type' => 'INNER',
                'conditions' => [
                    'User.id = Student.user_id'
                ]
            ]
        ];
        $condition = 'Student.id IN (' . implode(',', $this->contactIds('student')) . ')';
        
        return Datatable::simple(
            $this->getModel(), $columns, $joins, $condition
        );
        
    }
    
}

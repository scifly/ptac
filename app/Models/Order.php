<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Throwable;

/**
 * App\Models\Order 订单
 *
 * @property int $id
 * @property string $ordersn 订单序列号
 * @property int $user_id 微信支付用户ID
 * @property int $pay_user_id 实际付款用户ID
 * @property int $status 订单状态
 * @property int $combo_type_id 套餐类型ID
 * @property int $payment 支付类型（直付、代缴）
 * @property string $transactionid 微信订单号
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read ComboType $comboType
 * @property-read User $user
 * @method static Builder|Order whereComboTypeId($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereOrdersn($value)
 * @method static Builder|Order wherePayUserId($value)
 * @method static Builder|Order wherePayment($value)
 * @method static Builder|Order whereStatus($value)
 * @method static Builder|Order whereTransactionid($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @method static Builder|Order whereUserId($value)
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @mixin Eloquent
 */
class Order extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'ordersn', 'user_id', 'pay_user_id',
        'status', 'combo_type_id', 'payment',
        'transactionid', 'created_at', 'updated_at',
    ];
    
    /** @return BelongsTo */
    function user() { return $this->belongsTo('App\Models\User'); }
    
    /** @return BelongsTo */
    function comboType() { return $this->belongsTo('App\Models\ComboType'); }
    
    /**
     * 订单列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Orders.id', 'dt' => 0],
            ['db' => 'User.realname', 'dt' => 1],
            ['db' => 'Orders.ordersn', 'dt' => 2],
            ['db' => 'ComboType.name', 'dt' => 3],
            ['db' => 'Orders.payment', 'dt' => 4],
            ['db' => 'Orders.transactionid', 'dt' => 5],
            ['db' => 'Orders.created_at', 'dt' => 6],
            ['db' => 'Orders.updated_at', 'dt' => 7],
            [
                'db'        => 'Orders.status', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    // 已支付, 待支付
                },
            ],
        ];
        $joins = [
            [
                'table'      => 'users',
                'alias'      => 'User',
                'type'       => 'INNER',
                'conditions' => [
                    'User.id = Orders.user_id',
                ],
            ],
            [
                'table'      => 'combo_types',
                'alias'      => 'ComboType',
                'type'       => 'INNER',
                'conditions' => [
                    'ComboType.id = Orders.combo_type_id',
                ],
            ],
            [
                'table'      => 'schools',
                'alias'      => 'School',
                'type'       => 'INNER',
                'conditions' => [
                    'School.id = ComboType.school_id',
                ],
            ],
        ];
        $condition = 'School.id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * 保存订单
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新订单
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
        
        return $this->revise(
            $this, $data, $id, null
        );
        
    }
    
    /**
     * （批量）删除订单
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
}

<?php
namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Order
 *
 * @property int $id
 * @property string $ordersn 订单序列号
 * @property int $user_id 微信支付用户ID
 * @property int $pay_user_id 实际付款用户ID
 * @property int $status 订单状态
 * @property int $combo_type_id 套餐类型ID
 * @property int $payment 支付类型（直付、代缴）
 * @property string $transactionid 微信订单号
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
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
 * @mixin \Eloquent
 * @property-read ComboType $comboType
 * @property-read User $user
 */
class Order extends Model {
    
    protected $table = 'orders';
    
    protected $fillable = [
        'ordersn', 'user_id', 'pay_user_id',
        'status', 'combo_type_id', 'payment',
        'transactionid', 'created_at', 'updated_at',
    ];
    
    /**
     * 返回指定订单所属的用户对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user() { return $this->belongsTo('App\Models\User'); }
    
    /**
     * 返回指定订单所属的套餐类型对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function comboType() {
        
        return $this->belongsTo('App\models\ComboType');
        
    }
    
    public function datatable() {
        
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
        ];
        
        return Datatable::simple($this, $columns, $joins);
        
    }
    
}

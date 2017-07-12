<?php

namespace App\Models;

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
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereComboTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereOrdersn($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order wherePayment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereTransactionid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\Order whereUserId($value)
 * @mixin \Eloquent
 */
class Order extends Model
{
    //
}

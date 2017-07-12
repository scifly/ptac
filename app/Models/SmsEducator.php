<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\SmsEducator
 *
 * @property int $id
 * @property int $educator_id 被充值者教职员工ID
 * @property int $user_id 充值者用户ID
 * @property string $statistic_time 统计时间
 * @property int $balance 可用条数
 * @property int $deposit_count 充值条数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled 类型：0-统计,1-充值
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsEducator whereBalance($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsEducator whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsEducator whereDepositCount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsEducator whereEducatorId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsEducator whereEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsEducator whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsEducator whereStatisticTime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsEducator whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Models\SmsEducator whereUserId($value)
 * @mixin \Eloquent
 */
class SmsEducator extends Model
{
    //
}

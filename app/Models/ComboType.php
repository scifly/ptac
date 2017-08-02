<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ComboType
 *
 * @property int $id
 * @property string $name 套餐名称
 * @property int $amount 套餐金额
 * @property int $discount 折扣比例(80,90)
 * @property int $school_id 套餐所属学校ID
 * @property int $months 有效月数
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ComboType whereAmount($value)
 * @method static Builder|ComboType whereCreatedAt($value)
 * @method static Builder|ComboType whereDiscount($value)
 * @method static Builder|ComboType whereEnabled($value)
 * @method static Builder|ComboType whereId($value)
 * @method static Builder|ComboType whereMonths($value)
 * @method static Builder|ComboType whereName($value)
 * @method static Builder|ComboType whereSchoolId($value)
 * @method static Builder|ComboType whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\Models\School $schools
 */
class ComboType extends Model
{
    protected $table = 'combo_types';
    protected $fillable = [
        'name',
        'amount',
        'discount',
        'school_id',
        'months',
        'enabled'
    ];

    /**
     * 套餐与学校
     */
    public function schools()
    {
        return $this->belongsTo('App\Models\school');
    }
}

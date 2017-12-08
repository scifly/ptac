<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ComboType 套餐类型
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
 * @property-read School $schools
 * @property-read School $school
 */
class ComboType extends Model {

    protected $table = 'combo_types';

    protected $fillable = [
        'name', 'amount', 'discount',
        'school_id', 'months', 'enabled',
    ];

    /**
     * 返回套餐类型所属的学校对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function school() { return $this->belongsTo('App\Models\school'); }

    public function datatable() {
        $columns = [
            ['db' => 'ComboType.id', 'dt' => 0],
            ['db' => 'ComboType.name', 'dt' => 1],
            ['db' => 'ComboType.amount', 'dt' => 2],
            ['db' => 'ComboType.discount', 'dt' => 3],
            ['db' => 'School.name as schoolname', 'dt' => 4],
            ['db' => 'ComboType.months', 'dt' => 5],
            ['db' => 'ComboType.created_at', 'dt' => 6],
            ['db' => 'ComboType.updated_at', 'dt' => 7],
            [
                'db' => 'ComboType.updated_at', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row);
                },
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = ComboType.school_id',
                ],
            ],
        ];

        return Datatable::simple($this, $columns, $joins);

    }

}

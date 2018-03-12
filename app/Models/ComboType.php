<?php

namespace App\Models;

use App\Facades\DatatableFacade as Datatable;
use App\Helpers\ModelTrait;
use App\Helpers\Snippet;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ComboType 套餐类型
 *
 * @property int $id
 * @property string $name 套餐名称
 * @property int $amount 套餐金额
 * @property int $discount 折扣比例(80,90)
 * @property int $school_id 套餐所属学校ID
 * @property int $months 有效月数
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
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
 * @mixin Eloquent
 * @property-read School $schools
 * @property-read School $school
 */
class ComboType extends Model {
    
    use ModelTrait;

    protected $table = 'combo_types';

    protected $fillable = [
        'name', 'amount', 'discount',
        'school_id', 'months', 'enabled',
    ];

    /**
     * 返回套餐类型所属的学校对象
     *
     * @return BelongsTo
     */
    function school() { return $this->belongsTo('App\Models\school'); }
    
    /**
     * 套餐类型列表
     *
     * @return array
     */
    function datatable() {
        
        $columns = [
            ['db' => 'ComboType.id', 'dt' => 0],
            ['db' => 'ComboType.name', 'dt' => 1],
            [
                'db' => 'ComboType.amount', 'dt' => 2,
                'formatter' => function($d) {
                    setlocale(LC_MONETARY, 'zh_CN.UTF-8');
                    return money_format('%.2n', $d);
                }
            ],
            [
                'db' => 'ComboType.discount', 'dt' => 3,
                'formatter' => function($d) {
                    return $d . '%';
                }
            ],
            [
                'db' => 'School.name as schoolname', 'dt' => 4,
                'formatter' => function($d) {
                    return sprintf(Snippet::ICON, 'fa-university') . $d;
                }
            ],
            ['db' => 'ComboType.months', 'dt' => 5],
            ['db' => 'ComboType.created_at', 'dt' => 6],
            ['db' => 'ComboType.updated_at', 'dt' => 7],
            [
                'db' => 'ComboType.enabled', 'dt' => 8,
                'formatter' => function ($d, $row) {
                    return Datatable::dtOps($d, $row, false);
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
        $condition = 'ComboType.school_id = ' . $this->schoolId();
        
        return Datatable::simple($this->getModel(), $columns, $joins, $condition);

    }

}

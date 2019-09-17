<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model, Relations\BelongsTo};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\ComboType 套餐类型
 *
 * @property int $id
 * @property int $school_id 套餐所属学校ID
 * @property string $name 套餐名称
 * @property int $amount 套餐金额
 * @property int $discount 折扣比例(80,90)
 * @property int $months 有效月数
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read School $school
 * @method static Builder|ComboType newModelQuery()
 * @method static Builder|ComboType newQuery()
 * @method static Builder|ComboType query()
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
 */
class ComboType extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'school_id', 'name', 'amount', 'discount', 'months', 'enabled',
    ];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\school'); }
    
    /**
     * 套餐类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'ComboType.id', 'dt' => 0],
            ['db' => 'ComboType.name', 'dt' => 1],
            [
                'db'        => 'ComboType.amount', 'dt' => 2,
                'formatter' => function ($d) {
                    setlocale(LC_MONETARY, 'zh_CN.UTF-8');
                    
                    return money_format('%.2n', $d);
                },
            ],
            [
                'db'        => 'ComboType.discount', 'dt' => 3,
                'formatter' => function ($d) {
                    return $d . '%';
                },
            ],
            ['db' => 'ComboType.months', 'dt' => 4],
            ['db' => 'ComboType.created_at', 'dt' => 5],
            ['db' => 'ComboType.updated_at', 'dt' => 6],
            [
                'db'        => 'ComboType.enabled', 'dt' => 7,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $condition = 'ComboType.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, null, $condition
        );
        
    }
    
    /**
     * 保存套餐类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新套餐类型
     *
     * @param array $data
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function modify(array $data, $id) {
    
        try {
            DB::transaction(function () use ($data, $id) {
                throw_if(
                    !$comboType = $this->find($id),
                    new Exception(__('messages.not_found'))
                );
                $comboType->update($data);
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
    /**
     * 移除套餐类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $this->purge([class_basename($this)], 'id', 'purge', $id);
                $this->purge(['Order'], 'combo_type_id', 'reset', $id);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}

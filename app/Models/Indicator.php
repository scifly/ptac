<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\{Carbon};
use Throwable;

/**
 * App\Models\Indicator
 *
 * @property int $id
 * @property int $school_id 所属学校id
 * @property string $name 名称
 * @property int $sign 0 - 减分，1 - 加分
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Evaluate[] $evals
 * @property-read int|null $evals_count
 * @property-read School $school
 * @method static Builder|Indicator newModelQuery()
 * @method static Builder|Indicator newQuery()
 * @method static Builder|Indicator query()
 * @method static Builder|Indicator whereCreatedAt($value)
 * @method static Builder|Indicator whereEnabled($value)
 * @method static Builder|Indicator whereId($value)
 * @method static Builder|Indicator whereName($value)
 * @method static Builder|Indicator whereRemark($value)
 * @method static Builder|Indicator whereSchoolId($value)
 * @method static Builder|Indicator whereSign($value)
 * @method static Builder|Indicator whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Indicator extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['school_id', 'name', 'sign', 'remark', 'enabled'];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return HasMany */
    function evals() { return $this->hasMany('App\Models\Evaluate'); }
    
    /** @return array */
    function index() {
        
        $columns = [
            ['db' => 'Indicator.id', 'dt' => 0],
            ['db' => 'Indicator.name', 'dt' => 1],
            [
                'db' => 'Indicator.sign', 'dt' => 2,
                'formatter' => function ($d) {
                    return $d ? '+' : '-';
                }
            ],
            ['db' => 'Indicator.remark', 'dt' => 3],
            ['db' => 'Indicator.created_at', 'dt' => 4],
            ['db' => 'Indicator.updated_at', 'dt' => 5],
            [
                'db' => 'Indicator.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                }
            ],
        ];
        $joins = [
            [
                'table' => 'schools',
                'alias' => 'School',
                'type' => 'INNER',
                'conditions' => [
                    'School.id = Indicator.school_id'
                ]
            ]
        ];
        $condition = 'Indicator.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, $joins, $condition
        );
        
    }
    
    /**
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
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
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id, [
            'purge.indicator_id' => ['Evaluate']
        ]);
        
    }
    
}

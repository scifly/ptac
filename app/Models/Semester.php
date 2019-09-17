<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\BelongsTo, Relations\HasMany};
use Illuminate\Support\Facades\{DB, Request};
use Throwable;

/**
 * App\Models\Semester 学期
 *
 * @property int $id
 * @property int $school_id 所属学校ID
 * @property string $name 学期名称
 * @property string|null $remark 备注
 * @property string $start_date 学期开始日期
 * @property string $end_date 学期截止日期
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read School $school
 * @method static Builder|Semester whereCreatedAt($value)
 * @method static Builder|Semester whereEnabled($value)
 * @method static Builder|Semester whereEndDate($value)
 * @method static Builder|Semester whereId($value)
 * @method static Builder|Semester whereName($value)
 * @method static Builder|Semester whereRemark($value)
 * @method static Builder|Semester whereSchoolId($value)
 * @method static Builder|Semester whereStartDate($value)
 * @method static Builder|Semester whereUpdatedAt($value)
 * @method static Builder|Semester newModelQuery()
 * @method static Builder|Semester newQuery()
 * @method static Builder|Semester query()
 * @mixin Eloquent
 * @property-read Collection|Evaluate[] $evals
 * @property-read int|null $evals_count
 */
class Semester extends Model {
    
    use ModelTrait;
    
    protected $fillable = [
        'school_id', 'name', 'remark',
        'start_date', 'end_date', 'enabled',
    ];
    
    /** @return BelongsTo */
    function school() { return $this->belongsTo('App\Models\School'); }
    
    /** @return HasMany */
    function evals() { return $this->hasMany('App\Models\Evaluate'); }
    
    /**
     * 学期列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'Semester.id', 'dt' => 0],
            ['db' => 'Semester.name as semestername', 'dt' => 1],
            ['db' => 'Semester.start_date', 'dt' => 2],
            ['db' => 'Semester.end_date', 'dt' => 3],
            ['db' => 'Semester.created_at', 'dt' => 4],
            ['db' => 'Semester.updated_at', 'dt' => 5],
            [
                'db'        => 'Semester.enabled', 'dt' => 6,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        $condition = 'Semester.school_id = ' . $this->schoolId();
        
        return Datatable::simple(
            $this, $columns, null, $condition
        );
        
    }
    
    /**
     * 创建学期
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新学期
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除学期
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                Request::replace(['ids' => $ids]);
                $this->purge(['Semester'], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}
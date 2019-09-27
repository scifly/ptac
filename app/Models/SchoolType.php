<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\HasMany};
use Illuminate\Support\Facades\{DB, Request};
use Throwable;

/**
 * App\Models\SchoolType 学校类型
 *
 * @property int $id
 * @property string $name 学校类型名称
 * @property string $color
 * @property string $icon
 * @property string $remark 学校类型备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|School[] $schools
 * @property-read int|null $schools_count
 * @method static Builder|SchoolType whereCreatedAt($value)
 * @method static Builder|SchoolType whereEnabled($value)
 * @method static Builder|SchoolType whereId($value)
 * @method static Builder|SchoolType whereName($value)
 * @method static Builder|SchoolType whereColor($value)
 * @method static Builder|SchoolType whereIcon($value)
 * @method static Builder|SchoolType whereRemark($value)
 * @method static Builder|SchoolType whereUpdatedAt($value)
 * @method static Builder|SchoolType newModelQuery()
 * @method static Builder|SchoolType newQuery()
 * @method static Builder|SchoolType query()
 * @mixin Eloquent
 */
class SchoolType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /** @return HasMany */
    function schools() { return $this->hasMany('App\Models\School'); }
    
    /**
     * 学校类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'SchoolType.id', 'dt' => 0],
            ['db' => 'SchoolType.name', 'dt' => 1],
            ['db' => 'SchoolType.remark', 'dt' => 2],
            ['db' => 'SchoolType.created_at', 'dt' => 3],
            ['db' => 'SchoolType.updated_at', 'dt' => 4],
            [
                'db'        => 'SchoolType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple($this, $columns);
        
    }
    
    /**
     * 保存学校类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新学校类型
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
     * （批量）删除学校类型
     *
     * @param $id
     * @return bool
     * @throws Throwable
     */
    function remove($id = null) {
        
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $schoolIds = School::whereIn('school_type_id', $ids)
                    ->pluck('id')->toArray();
                Request::replace(['ids' => $schoolIds]);
                (new School)->remove();
                Request::replace(['ids' => $ids]);
                $this->purge(['SchoolType'], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}

<?php
namespace App\Models;

use App\Facades\Datatable;
use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
use ReflectionClass;
use Throwable;

/**
 * App\Models\DepartmentType 部门类型
 *
 * @property int $id
 * @property string $name 部门类型名称
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Department[] $departments
 * @method static Builder|DepartmentType whereCreatedAt($value)
 * @method static Builder|DepartmentType whereEnabled($value)
 * @method static Builder|DepartmentType whereId($value)
 * @method static Builder|DepartmentType whereName($value)
 * @method static Builder|DepartmentType whereRemark($value)
 * @method static Builder|DepartmentType whereUpdatedAt($value)
 * @method static Builder|DepartmentType newModelQuery()
 * @method static Builder|DepartmentType newQuery()
 * @method static Builder|DepartmentType query()
 * @mixin Eloquent
 */
class DepartmentType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'remark', 'enabled'];
    
    /**
     * 获取指定部门类型包含的所有部门对象
     *
     * @return HasMany
     */
    function departments() { return $this->hasMany('App\Models\Department'); }
    
    /**
     * 部门类型列表
     *
     * @return array
     */
    function index() {
        
        $columns = [
            ['db' => 'DepartmentType.id', 'dt' => 0],
            ['db' => 'DepartmentType.name', 'dt' => 1],
            ['db' => 'DepartmentType.remark', 'dt' => 2],
            ['db' => 'DepartmentType.created_at', 'dt' => 3],
            ['db' => 'DepartmentType.updated_at', 'dt' => 4],
            [
                'db'        => 'DepartmentType.enabled', 'dt' => 5,
                'formatter' => function ($d, $row) {
                    return Datatable::status($d, $row, false);
                },
            ],
        ];
        
        return Datatable::simple(
            $this->getModel(), $columns
        );
        
    }
    
    /**
     * 创建部门类型
     *
     * @param array $data
     * @return bool
     */
    function store(array $data) {
        
        return $this->create($data) ? true : false;
        
    }
    
    /**
     * 更新部门类型
     *
     * @param array $data
     * @param $id
     * @return bool
     */
    function modify(array $data, $id) {
        
        return $this->find($id)->update($data);
        
    }
    
    /**
     * 删除部门类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
    
        try {
            DB::transaction(function () use ($id) {
                $ids = $id ? [$id] : array_values(Request::input('ids'));
                $departmentIds = Department::whereIn('department_type_id', $ids)
                    ->pluck('id')->unique()->values()->toArray();
                Request::replace(['ids' => $departmentIds]);
                (new Department)->remove();
                Request::replace(['ids' => $ids]);
                $this->purge([class_basename($this)], 'id');
            });
        } catch (Exception $e) {
            throw $e;
        }
    
        return true;
        
    }
    
}

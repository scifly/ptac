<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\HasMany};
use Illuminate\Support\Facades\{DB, Request};
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
 * @property-read int|null $departments_count
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

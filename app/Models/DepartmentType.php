<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Collection, Model, Relations\HasMany};
use Throwable;

/**
 * App\Models\DepartmentType 部门类型
 *
 * @property int $id
 * @property string $name 部门类型名称
 * @property string|null $color 图标颜色
 * @property string|null $icon 图标class
 * @property string|null $remark 备注
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @property-read Collection|Department[] $depts
 * @property-read int|null $depts_count
 * @method static Builder|DepartmentType whereCreatedAt($value)
 * @method static Builder|DepartmentType whereEnabled($value)
 * @method static Builder|DepartmentType whereId($value)
 * @method static Builder|DepartmentType whereName($value)
 * @method static Builder|DepartmentType whereColor($value)
 * @method static Builder|DepartmentType whereIcon($value)
 * @method static Builder|DepartmentType whereRemark($value)
 * @method static Builder|DepartmentType whereUpdatedAt($value)
 * @method static Builder|DepartmentType newModelQuery()
 * @method static Builder|DepartmentType newQuery()
 * @method static Builder|DepartmentType query()
 * @mixin Eloquent
 */
class DepartmentType extends Model {
    
    use ModelTrait;
    
    protected $fillable = ['name', 'color', 'icon', 'remark', 'enabled'];
    
    /** @return HasMany */
    function depts() { return $this->hasMany('App\Models\Department', 'department_type_id'); }
    
    /**
     * 删除部门类型
     *
     * @param $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge($id);
        
    }
    
}

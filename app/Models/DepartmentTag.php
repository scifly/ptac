<?php
namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DepartmentTag
 *
 * @package App\Models
 * @property int $id
 * @property int $department_id 部门id
 * @property int $tag_id 标签id
 * @property Carbon|null $created_at 创建于
 * @property Carbon|null $updated_at 更新于
 * @property int $enabled 状态
 * @method static Builder|DepartmentTag whereCreatedAt($value)
 * @method static Builder|DepartmentTag whereDepartmentId($value)
 * @method static Builder|DepartmentTag whereEnabled($value)
 * @method static Builder|DepartmentTag whereId($value)
 * @method static Builder|DepartmentTag whereTagId($value)
 * @method static Builder|DepartmentTag whereUpdatedAt($value)
 * @method static Builder|DepartmentTag newModelQuery()
 * @method static Builder|DepartmentTag newQuery()
 * @method static Builder|DepartmentTag query()
 * @mixin Eloquent
 */
class DepartmentTag extends Model {
    
    protected $table = 'departments_tags';
    
    protected $fillable = ['department_id', 'tag_id', 'enabled'];
    
}

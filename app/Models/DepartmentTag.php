<?php
namespace App\Models;

use App\Helpers\Constant;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Throwable;

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
    
    /**
     * 保存指定部门所属的标签记录
     *
     * @param $departmentId
     * @param array $tagIds
     * @return bool
     * @throws Throwable
     */
    function storeByDepartmentId($departmentId, array $tagIds) {
        
        try {
            DB::transaction(function () use ($departmentId, $tagIds) {
                $records = [];
                foreach ($tagIds as $tagId) {
                    $records[] = [
                        'department_id' => $departmentId,
                        'tag_id'  => $tagId,
                        'enabled' => Constant::ENABLED,
                    ];
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 保存指定标签所包含的部门记录
     *
     * @param $tagId
     * @param array $departmentIds
     * @return bool
     * @throws Throwable
     */
    function storeByTagId($tagId, array $departmentIds) {
        
        try {
            DB::transaction(function () use ($tagId, $departmentIds) {
                $records = [];
                foreach ($departmentIds as $departmentId) {
                    $records[] = [
                        'department_id' => $departmentId,
                        'tag_id'  => $tagId,
                        'enabled' => Constant::ENABLED,
                    ];
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}

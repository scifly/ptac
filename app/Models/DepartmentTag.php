<?php
namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Relations\BelongsTo, Relations\Pivot};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Request;
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
 * @property-read Department $dept
 * @property-read Tag $tag
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
class DepartmentTag extends Pivot {
    
    use ModelTrait;
    
    protected $fillable = ['department_id', 'tag_id', 'enabled'];
    
    /** @return BelongsTo */
    function tag() { return $this->belongsTo('App\Models\Tag'); }
    
    /** @return BelongsTo */
    function dept() { return $this->belongsTo('App\Models\Department', 'department_id'); }
    
    /**
     * 按部门id保存部门标签绑定关系
     *
     * @param $deptId
     * @param array $tagIds
     * @return bool
     * @throws Throwable
     */
    function storeByDeptId($deptId, array $tagIds) {
        
        try {
            DB::transaction(function () use ($deptId, $tagIds) {
                $condition = $record = array_combine(
                    ['department_id', 'enabled'],
                    [$deptId, Constant::ENABLED]
                );
                $this->where($condition)->delete();
                foreach ($tagIds as $tagId) {
                    $record['tag_id'] = $tagId;
                    $records[] = $record;
                }
                $this->insert($records ?? []);
                if (!empty($tagIds)) {
                    Request::merge(['ids' => $tagIds]);
                    (new Tag)->modify();
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * @param null $id
     * @throws Throwable
     */
    function remove($id = null) {
        
        $this->purge($id);
        
    }
    
}

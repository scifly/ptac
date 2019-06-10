<?php
namespace App\Models;

use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\{Builder, Relations\Pivot};
use Throwable;

/**
 * App\Models\TagUser
 *
 * @property int $id
 * @property int $tag_id 标签id
 * @property int $user_id 用户id
 * @property Carbon|null $created_at 创建于
 * @property Carbon|null $updated_at 更新于
 * @property int $enabled 状态
 * @method static Builder|TagUser whereCreatedAt($value)
 * @method static Builder|TagUser whereEnabled($value)
 * @method static Builder|TagUser whereId($value)
 * @method static Builder|TagUser whereTagId($value)
 * @method static Builder|TagUser whereUpdatedAt($value)
 * @method static Builder|TagUser whereUserId($value)
 * @method static Builder|TagUser newModelQuery()
 * @method static Builder|TagUser newQuery()
 * @method static Builder|TagUser query()
 * @mixin Eloquent
 */
class TagUser extends Pivot {
    
    use ModelTrait;
    
    protected $fillable = ['tag_id', 'user_id', 'enabled'];
    
    /**
     * 删除标签
     *
     * @param null $id
     * @return bool|null
     * @throws Throwable
     */
    function remove($id = null) {
        
        return $this->purge(['TagUser'], 'id', 'purge', $id);
        
    }
    
}

<?php
namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;
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
class TagUser extends Model {
    
    use ModelTrait;
    
    protected $table = 'tags_users';
    
    protected $fillable = ['tag_id', 'user_id', 'enabled'];
    
    /**
     * 保存指定用户所属的标签记录
     *
     * @param $userId
     * @param array $tagIds
     * @return bool
     * @throws Throwable
     */
    function storeByUserId($userId, array $tagIds) {
        
        try {
            DB::transaction(function () use ($userId, $tagIds) {
                $records = [];
                foreach ($tagIds as $tagId) {
                    $records[] = [
                        'user_id' => $userId,
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
     * 保存指定标签所包含的用户记录
     *
     * @param $tagId
     * @param array $userIds
     * @return bool
     * @throws Throwable
     */
    function storeByTagId($tagId, array $userIds) {
        
        try {
            DB::transaction(function () use ($tagId, $userIds) {
                $records = [];
                foreach ($userIds as $userId) {
                    $records[] = [
                        'user_id' => $userId,
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
     * 删除标签
     *
     * @param null $value
     * @param null $field
     * @param bool $soft
     * @return bool|null
     * @throws Exception
     */
    function remove($value = null, $field = null, $soft = false) {

        return $this->clear($this, $value, $field, $soft) ? true : false;
        
    }
    
}

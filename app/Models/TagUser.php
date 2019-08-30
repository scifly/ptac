<?php
namespace App\Models;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Relations\Pivot};
use Illuminate\Support\Facades\DB;
use Request;
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
     * 按用户id保存标签用户绑定关系
     *
     * @param $userId
     * @param array $tagIds
     * @param null $custodian
     * @return bool
     * @throws Throwable
     */
    function storeByUserId($userId, array $tagIds, $custodian = null) {
    
        try {
            DB::transaction(function () use ($userId, $tagIds, $custodian) {
                $enabled = $custodian ? Constant::DISABLED : Constant::ENABLED;
                $condition = $record = array_combine(
                    ['user_id', 'enabled'], [$userId, $enabled]
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

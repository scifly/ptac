<?php
namespace App\Models;

use App\Helpers\Constant;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\ActionGroup
 *
 * @property int $id
 * @property int $action_id
 * @property int $group_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ActionGroup whereActionId($value)
 * @method static Builder|ActionGroup whereCreatedAt($value)
 * @method static Builder|ActionGroup whereEnabled($value)
 * @method static Builder|ActionGroup whereGroupId($value)
 * @method static Builder|ActionGroup whereId($value)
 * @method static Builder|ActionGroup whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ActionGroup extends Model {
    
    protected $table = 'actions_groups';
    
    protected $fillable = ['action_id', 'group_id', 'enabled'];
    
    /**
     * 根据groupId保存所有记录
     *
     * @param $groupId
     * @param array $ids
     * @throws Exception
     * @throws \Throwable
     */
    function storeByGroupId($groupId, array $ids = []) {
        
        try {
            DB::transaction(function () use ($groupId, $ids) {
                # step 1: 删除group_id等于$groupId的所有记录
                self::whereGroupId($groupId)->delete();
                # step 2: 创建ids对应的所有记录
                $records = [];
                foreach ($ids as $id) {
                    $records[] = [
                        'group_id'   => $groupId,
                        'action_id'  => $id,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'enabled'    => Constant::ENABLED,
                    ];
                }
                self::insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
    }
    
}

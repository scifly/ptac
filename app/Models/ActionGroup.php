<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\ActionGroup
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $action_id
 * @property int $group_id
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|ActionGroup whereActionId($value)
 * @method static Builder|ActionGroup whereCreatedAt($value)
 * @method static Builder|ActionGroup whereEnabled($value)
 * @method static Builder|ActionGroup whereGroupId($value)
 * @method static Builder|ActionGroup whereId($value)
 * @method static Builder|ActionGroup whereUpdatedAt($value)
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
    public function storeByGroupId($groupId, array $ids = []) {

        try {
            DB::transaction(function () use ($groupId, $ids) {
                # step 1: 删除group_id等于$groupId的所有记录
                $this->where('group_id', $groupId)->delete();
                # step 2: 创建ids中的所有记录
                foreach ($ids as $id) {
                    $this->create([
                        'group_id' => $groupId,
                        'action_id' => $id,
                        'enabled' => 1,
                    ]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }

    }

}

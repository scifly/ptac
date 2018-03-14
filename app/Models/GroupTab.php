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
 * App\Models\GroupTab
 *
 * @mixin Eloquent
 * @property int $id
 * @property int $group_id
 * @property int $tab_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|GroupTab whereCreatedAt($value)
 * @method static Builder|GroupTab whereEnabled($value)
 * @method static Builder|GroupTab whereGroupId($value)
 * @method static Builder|GroupTab whereId($value)
 * @method static Builder|GroupTab whereTabId($value)
 * @method static Builder|GroupTab whereUpdatedAt($value)
 */
class GroupTab extends Model {

    protected $table = 'groups_tabs';

    protected $fillable = ['group_id', 'tab_id', 'enabled'];
    
    /**
     * 根据角色id保存所有卡片id
     *
     * @param $groupId
     * @param array $ids
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    function storeByGroupId($groupId, array $ids = []) {
        
        try {
            DB::transaction(function () use ($groupId, $ids) {
                self::whereGroupId($groupId)->delete();
                $values = [];
                foreach ($ids as $id) {
                    $values[] = [
                        'group_id' => $groupId,
                        'tab_id' => $id,
                        'enabled' => Constant::ENABLED,
                    ];
                }
                $this->insert($values);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;

    }

}

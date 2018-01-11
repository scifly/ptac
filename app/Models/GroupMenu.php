<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\GroupMenu
 *
 * @mixin Eloquent
 * @property int $id
 * @property int $group_id
 * @property int $menu_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $enabled
 * @method static Builder|GroupMenu whereCreatedAt($value)
 * @method static Builder|GroupMenu whereEnabled($value)
 * @method static Builder|GroupMenu whereGroupId($value)
 * @method static Builder|GroupMenu whereId($value)
 * @method static Builder|GroupMenu whereMenuId($value)
 * @method static Builder|GroupMenu whereUpdatedAt($value)
 */
class GroupMenu extends Model {

    protected $table = 'groups_menus';

    protected $fillable = ['group_id', 'menu_id', 'enabled'];
    
    /**
     * 根据角色id保存所有菜单id
     *
     * @param $groupId
     * @param array $ids
     * @return bool
     * @throws Exception
     * @throws \Throwable
     */
    static function storeByGroupId($groupId, array $ids = []) {
        
        try {
            DB::transaction(function () use ($groupId, $ids) {
                self::whereGroupId($groupId)->delete();
                foreach ($ids as $id) {
                    self::create([
                        'group_id' => $groupId,
                        'menu_id' => $id,
                        'enabled' => 1,
                    ]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;

    }

}

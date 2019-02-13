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
 * App\Models\GroupMenu
 *
 * @mixin Eloquent
 * @property int $id
 * @property int $group_id
 * @property int $menu_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $enabled
 * @property-read \App\Models\Menu $menu
 * @method static Builder|GroupMenu whereCreatedAt($value)
 * @method static Builder|GroupMenu whereEnabled($value)
 * @method static Builder|GroupMenu whereGroupId($value)
 * @method static Builder|GroupMenu whereId($value)
 * @method static Builder|GroupMenu whereMenuId($value)
 * @method static Builder|GroupMenu whereUpdatedAt($value)
 * @method static Builder|GroupMenu newModelQuery()
 * @method static Builder|GroupMenu newQuery()
 * @method static Builder|GroupMenu query()
 */
class GroupMenu extends Model {
    
    protected $table = 'groups_menus';
    
    protected $fillable = ['group_id', 'menu_id', 'enabled'];
    
    /**
     * 返回指定记录所属的菜单对象
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    function menu() { return $this->belongsTo('App\Models\Menu'); }
    
    /**
     * 根据角色id保存所有菜单id
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
                $records = [];
                foreach ($ids as $id) {
                    $records[] = array_combine(Constant::GM_FIELDS, [
                        $groupId, $id,
                        now()->toDateTimeString(),
                        now()->toDateTimeString(),
                        Constant::ENABLED,
                    ]);
                }
                $this->insert($records);
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
}

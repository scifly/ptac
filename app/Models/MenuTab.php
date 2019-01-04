<?php
namespace App\Models;

use App\Helpers\Constant;
use Carbon\Carbon;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\{Builder, Model};
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * App\Models\MenuTab
 *
 * @mixin Eloquent
 * @property int $id
 * @property int $menu_id 卡片所属菜单ID
 * @property int $tab_id 卡片ID
 * @property int|null $tab_order 卡片顺序值
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|MenuTab whereCreatedAt($value)
 * @method static Builder|MenuTab whereEnabled($value)
 * @method static Builder|MenuTab whereId($value)
 * @method static Builder|MenuTab whereMenuId($value)
 * @method static Builder|MenuTab whereTabId($value)
 * @method static Builder|MenuTab whereTabOrder($value)
 * @method static Builder|MenuTab whereUpdatedAt($value)
 * @method static Builder|MenuTab newModelQuery()
 * @method static Builder|MenuTab newQuery()
 * @method static Builder|MenuTab query()
 */
class MenuTab extends Model {
    
    protected $table = 'menus_tabs';
    
    protected $fillable = ['menu_id', 'tab_id', 'tab_order', 'enabled'];
    
    /**
     * 按菜单ID保存记录
     *
     * @param $menuId
     * @param array $tabIds
     * @return bool
     * @throws Throwable
     */
    function storeByMenuId($menuId, array $tabIds) {
        
        try {
            DB::transaction(function () use ($menuId, $tabIds) {
                $records = [];
                foreach ($tabIds as $tabId) {
                    $records[] = [
                        'menu_id'    => $menuId,
                        'tab_id'     => $tabId,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'enabled'    => Constant::ENABLED,
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
     * 按卡片ID保存记录
     *
     * @param $tabId
     * @param array $menuIds
     * @return bool
     * @throws Throwable
     */
    function storeByTabId($tabId, array $menuIds) {
        
        try {
            DB::transaction(function () use ($tabId, $menuIds) {
                $records = [];
                foreach ($menuIds as $menuId) {
                    $records[] = [
                        'menu_id'    => $menuId,
                        'tab_id'     => $tabId,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString(),
                        'enabled'    => Constant::ENABLED,
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
     * 保存卡片排序
     *
     * @param $menuId
     * @param array $ranks
     * @return bool
     * @throws Throwable
     */
    function storeTabRanks($menuId, array $ranks) {
        
        try {
            DB::transaction(function () use ($menuId, $ranks) {
                foreach ($ranks as $id => $rank) {
                    self::whereMenuId($menuId)
                        ->where('tab_id', $id)
                        ->update(['tab_order' => $rank + 1]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;
        
    }
    
    /**
     * 根据指定菜单包含的卡片Id数组
     *
     * @param $menuId
     * @return array
     */
    function tabIdsByMenuId($menuId) {
        
        return self::whereMenuId($menuId)
            ->orderBy('tab_order')
            ->pluck('tab_id')
            ->toArray();
        
    }
    
}

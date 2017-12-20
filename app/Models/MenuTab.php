<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\MenuTab
 *
 * @mixin \Eloquent
 * @property int $id
 * @property int $menu_id 卡片所属菜单ID
 * @property int $tab_id 卡片ID
 * @property int|null $tab_order 卡片顺序值
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @property int $enabled
 * @method static Builder|MenuTab whereCreatedAt($value)
 * @method static Builder|MenuTab whereEnabled($value)
 * @method static Builder|MenuTab whereId($value)
 * @method static Builder|MenuTab whereMenuId($value)
 * @method static Builder|MenuTab whereTabId($value)
 * @method static Builder|MenuTab whereTabOrder($value)
 * @method static Builder|MenuTab whereUpdatedAt($value)
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
     * @throws Exception
     * @throws \Throwable
     */
    public function storeByMenuId($menuId, array $tabIds) {
        try {
            DB::transaction(function () use ($menuId, $tabIds) {
                foreach ($tabIds as $tabId) {
                    $this->create([
                        'menu_id' => $menuId,
                        'tab_id' => $tabId,
                        'enabled' => 1,
                    ]);
                }
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
     * @throws Exception
     * @throws \Throwable
     */
    public function storeByTabId($tabId, array $menuIds) {
        
        try {
            DB::transaction(function () use ($tabId, $menuIds) {
                foreach ($menuIds as $menuId) {
                    $this->create([
                        'menu_id' => $menuId,
                        'tab_id' => $tabId,
                        'enabled' => 1,
                    ]);
                }
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
     * @throws Exception
     * @throws \Throwable
     */
    public function storeTabRanks($menuId, array $ranks) {
        
        try {
            DB::transaction(function () use ($menuId, $ranks) {
                foreach ($ranks as $id => $rank) {
                    $this::whereMenuId($menuId)->where('tab_id', $id)
                        ->update(['tab_order' => $rank + 1]);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        
        return true;

    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

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
    protected $fillable = [
        'menu_id',
        'tab_id',
        'tab_order',
        'enabled'
    ];
    
    public function store($menuId, array $tabIds) {
        
        foreach ($tabIds as $tabId) {
            $this->create([
                'menu_id' => $menuId,
                'tab_id' => $tabId,
                'enabled' => 1
            ]);
        }
        
    }
    
}

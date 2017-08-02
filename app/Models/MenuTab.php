<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MenuTab
 *
 * @mixin \Eloquent
 */
class MenuTab extends Model {
    
    protected $table = 'menus_tabs';
    protected $fillable = [
        'menu_id',
        'tab_id',
        'tab_order',
        'enabled'
    ];
    
}

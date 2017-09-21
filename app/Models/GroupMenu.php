<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupMenu
 *
 * @mixin \Eloquent
 */
class GroupMenu extends Model {
    
    protected $table = 'groups_menus';
    
    protected $fillable = ['group_id', 'menu_id', 'enabled'];
    
}

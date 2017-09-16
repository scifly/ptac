<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupMenu extends Model {
    
    protected $table = 'groups_menus';
    
    protected $fillable = ['group_id', 'menu_id', 'enabled'];
    
}

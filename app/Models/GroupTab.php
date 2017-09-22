<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupTab
 *
 * @mixin \Eloquent
 */
class GroupTab extends Model {
    
    protected $table = 'groups_tabs';
    
    protected $fillable = ['group_id', 'tab_id', 'enabled'];
    
}

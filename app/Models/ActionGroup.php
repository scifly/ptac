<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ActionGroup
 *
 * @mixin \Eloquent
 */
class ActionGroup extends Model {
    
    protected $table = 'actions_groups';
    
    protected $fillable = ['action_id', 'group_id', 'enabled'];
    
}

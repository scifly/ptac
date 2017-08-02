<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TabAction
 *
 * @mixin \Eloquent
 */
class TabAction extends Model {
    
    protected $table = 'tabs_actions';
    protected $fillable = [
        'tab_id',
        'action_id',
        'default',
        'enabled'
    ];
    
}

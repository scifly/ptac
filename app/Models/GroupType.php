<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupType
 *
 * @property-read Collection|Group[] $groups
 * @mixin \Eloquent
 */
class GroupType extends Model {

    public function groups() { return $this->hasMany('App\Models\Group');}
    
}

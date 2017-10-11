<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GroupType
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Group[] $groups
 * @mixin \Eloquent
 */
class GroupType extends Model
{

    public function groups() { return $this->hasMany('App\Models\Group');}
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\GroupType
 *
 * @property-read Collection|Group[] $groups
 * @mixin \Eloquent
 */
class GroupType extends Model {
    
    /**
     * 获取指定角色类型包含的所有角色对象
     *
     * @return HasMany
     */
    public function groups() { return $this->hasMany('App\Models\Group'); }

}

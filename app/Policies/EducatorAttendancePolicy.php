<?php

namespace App\Policies;

use App\Helpers\Constant;
use App\Models\ActionGroup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EducatorAttendancePolicy {
    
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function sde(User $user) {
    
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
    
        return ActionGroup::whereGroupId($user->group_id)->first() ? true : false;
        
    }
}

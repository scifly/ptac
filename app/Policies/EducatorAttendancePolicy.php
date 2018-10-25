<?php

namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\PolicyTrait;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class EducatorAttendancePolicy
 * @package App\Policies
 */
class EducatorAttendancePolicy {
    
    use HandlesAuthorization, PolicyTrait;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @return bool
     */
    function operation(User $user) {
    
        return in_array($user->role(), Constant::SUPER_ROLES)
            ? true : $this->action($user);
        
    }
    
}

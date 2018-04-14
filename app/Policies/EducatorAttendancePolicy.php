<?php

namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\PolicyTrait;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
    
    function stat(User $user) {
        
        return $this->permit($user);
        
    }
    
    function detail(User $user) {
        
        return $this->permit($user);
        
    }
    
    function export(User $user) {
        
        return $this->permit($user);
        
    }
    
    private function permit(User $user) {
    
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
    
        return $this->action($user);
        
    }
}

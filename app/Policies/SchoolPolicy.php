<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait};
use App\Models\{School, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SchoolPolicy
 * @package App\Policies
 */
class SchoolPolicy {
    
    use HandlesAuthorization, ModelTrait;
    
    /**
     * @param User $user
     * @param School $school
     * @return bool
     */
    public function operation(User $user, School $school = null) {
        
        return in_array($user->role(), Constant::SUPER_ROLES)
            && (!$school ? true : in_array($school->id, $this->schoolIds()));
        
    }
    
}
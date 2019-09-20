<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ActionPolicy
 * @package App\Policies
 */
class ActionPolicy {
    
    use HandlesAuthorization;
    
    /**
     * @param User $user
     * @return bool
     */
    function operation(User $user) {
        
        return $user->role() == '运营';
        
    }
    
}

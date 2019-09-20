<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class CompanyPolicy
 * @package App\Policies
 */
class CompanyPolicy {
    
    use HandlesAuthorization;
    
    /**
     * @param User $user
     * @return bool
     */
    function operation(User $user) {
        
        return $user->role() == '运营';
        
    }
    
}
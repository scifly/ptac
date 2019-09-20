<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SchoolTypePolicy
 * @package App\Policies
 */
class SchoolTypePolicy {
    
    use HandlesAuthorization;
    
    /**
     * @param User $user
     * @return bool
     */
    public function operation(User $user) {
        
        return $user->role() == '运营';
        
    }
    
}

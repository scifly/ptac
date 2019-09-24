<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class TabPolicy
 * @package App\Policies
 */
class TabPolicy {
    
    use HandlesAuthorization;
    
    /**
     * @param User $user
     * @return bool
     */
    public function operation(User $user) {
        
        return $user->role() == '运营';
        
    }
    
}

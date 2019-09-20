<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MenuTypePolicy
 * @package App\Policies
 */
class MenuTypePolicy {
    
    use HandlesAuthorization;
    
    /**
     * @param User $user
     * @return bool
     */
    public function operation(User $user) {
        
        return $user->role() == '运营';
        
    }
    
}

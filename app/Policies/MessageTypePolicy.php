<?php
namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MessageTypePolicy
 * @package App\Policies
 */
class MessageTypePolicy {
    
    use HandlesAuthorization;
    
    /**
     * @param User $user
     * @return bool
     */
    public function operation(User $user) {
        
        return $user->role() == '运营';
        
    }
    
}

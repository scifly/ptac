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
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @return bool
     */
    function operation(User $user) {
        
        return $user->role() == '运营';
        
    }
    
}

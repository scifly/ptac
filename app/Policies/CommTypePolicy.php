<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\CommType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class CommTypePolicy
 * @package App\Policies
 */
class CommTypePolicy {
    
    use HandlesAuthorization;
    
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
     * @param CommType|null $ct
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, CommType $ct = null, $abort = false) {
    
        abort_if(
            $abort && !$ct,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->role() == '运营';
        
    }
    
}

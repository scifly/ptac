<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\ActionType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ActionTypePolicy
 * @package App\Policies
 */
class ActionTypePolicy {
    
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
     * 判断权限
     *
     * @param User $user
     * @param ActionType|null $at
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, ActionType $at = null, $abort = false) {
        
        abort_if(
            $abort && !$at,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}

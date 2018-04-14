<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\Action;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
     * @param Action|null $action
     * @param bool $abort
     * @return bool
     */
    function action(User $user, Action $action = null, $abort = false) {
        
        abort_if(
            $abort && !$action,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}

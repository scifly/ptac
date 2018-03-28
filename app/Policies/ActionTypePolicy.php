<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\ActionType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
    
    public function cs(User $user) {
        
        return $user->group->name == '运营';
        
    }
    
    public function eud(User $user, ActionType $at) {
        
        abort_if(
            !$at,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
}

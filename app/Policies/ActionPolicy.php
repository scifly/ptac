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
    
    public function edit(User $user, Action $action) {
        
        return $this->permit($user, $action);
        
    }
    
    public function update(User $user, Action $action) {
        
        return $this->permit($user, $action);
        
    }
    
    private function permit(User $user, Action $action) {
        
        abort_if(
            !$action,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
}

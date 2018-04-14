<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\AlertType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AlertTypePolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function create(User $user) {
        
        return $this->permit($user);
        
    }
    
    public function store(User $user) {
        
        return $this->permit($user);
        
    }
    
    public function edit(User $user, AlertType $at) {
        
        return $this->permit($user, $at, true);
        
    }
    
    public function update(User $user, AlertType $at) {
        
        return $this->permit($user, $at, true);
        
    }
    
    public function destroy(User $user, AlertType $at) {
        
        return $this->permit($user, $at, true);
        
    }
    
    /**
     * 判断权限
     *
     * @param User $user
     * @param AlertType|null $at
     * @param bool $abort
     * @return bool
     */
    private function permit(User $user, AlertType $at = null, $abort = false) {
        
        abort_if(
            $abort && !$at,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}

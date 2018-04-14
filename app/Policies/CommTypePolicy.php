<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\CommType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
    
    public function create(User $user) {
        
        return $this->permit($user);
        
    }
    
    public function store(User $user) {
        
        return $this->permit($user);
        
    }
    
    public function edit(User $user, CommType $ct) {
        
        return $this->permit($user, $ct, true);
        
    }
    
    public function update(User $user, CommType $ct) {
        
        return $this->permit($user, $ct, true);
        
    }
    
    public function destroy(User $user, CommType $ct) {
        
        return $this->permit($user, $ct,true);
        
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param CommType|null $ct
     * @param bool $abort
     * @return bool
     */
    private function permit(User $user, CommType $ct = null, $abort = false) {
    
        abort_if(
            $abort && !$ct,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}

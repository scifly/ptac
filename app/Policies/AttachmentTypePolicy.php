<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\AttachmentType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttachmentTypePolicy {
    
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
    
    public function edit(User $user, AttachmentType $at) {
        
        return $this->permit($user, $at, true);
        
    }
    
    public function update(User $user, AttachmentType $at) {
        
        return $this->permit($user, $at, true);
        
    }
    
    public function destroy(User $user, AttachmentType $at) {
        
        return $this->permit($user, $at, true);
        
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param AttachmentType|null $at
     * @param bool $abort
     * @return bool
     */
    private function permit(User $user, AttachmentType $at = null, $abort = false) {
        
        abort_if(
            $abort && !$at,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}

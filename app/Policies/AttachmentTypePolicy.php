<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\AttachmentType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AttachmentTypePolicy
 * @package App\Policies
 */
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
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param AttachmentType|null $at
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, AttachmentType $at = null, $abort = false) {
        
        abort_if(
            $abort && !$at,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->role() == '运营';
        
    }
    
}

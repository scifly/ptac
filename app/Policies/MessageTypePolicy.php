<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\MessageType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MessageTypePolicy
 * @package App\Policies
 */
class MessageTypePolicy {
    
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
     * @param MessageType|null $mt
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, MessageType $mt = null, $abort = false) {
        
        abort_if(
            $abort && !$mt,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}

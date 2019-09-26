<?php
namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MessagePolicy
 * @package App\Policies
 */
class MessagePolicy {
    
    use HandlesAuthorization;
    
    /**
     * @param User $user
     * @param Message|null $message
     */
    function operation(User $user, Message $message = null) {
    
    }
    
}

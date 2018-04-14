<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class EventPolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function destroy(User $user, Event $event) {
        
        return $this->objectPerm($user, $event);
        
    }
    
    private function objectPerm(User $user, Event $event) {
        
        abort_if(
            !$event,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $event->user_id == $user->id;
        
    }
    
}

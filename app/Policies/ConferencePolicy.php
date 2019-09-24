<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{Conference, Message, Room, School, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;

/**
 * Class ConferencePolicy
 * @package App\Policies
 */
class ConferencePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Conference $conference
     * @return bool
     * @throws \Exception
     */
    function operation(User $user, Conference $conference = null) {
        
        if (!$user->educator) return false;
        $perm = true;
        [$userId, $roomId, $messageId] = array_map(
            function ($field) use ($conference) {
                return $this->field($field, $conference);
            }, ['user_id', 'room_id', 'message_id']
        );
        if ($userId) {
            in_array($user->role(), Constant::SUPER_ROLES) ?: $perm &= $userId == Auth::id();
        }
        !$roomId ?: $perm &= Room::find($roomId)->building->school_id == $this->schoolId();
        if ($messageId) {
            $message = Message::find($messageId);
            $perm &= collect(explode(',', $this->visibleUserIds()))
                ->has($message->targetUserIds($message));
        }
        
        return $this->action($user) && $perm;
        
    }
    
}

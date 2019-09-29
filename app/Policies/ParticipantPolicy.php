<?php

namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Participant, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class ParticipantPolicy
 * @package App\Policies
 */
class ParticipantPolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @return bool
     */
    function operation(User $user) {
        
        if (!$user->educator) return false;
        $educatorIds = Participant::whereConferenceId(
            Request::input('conference_id')
        )->pluck('educator_id')->flip();
        
        return $this->action($user) && $educatorIds->has($user->educator->id);
        
    }
    
}

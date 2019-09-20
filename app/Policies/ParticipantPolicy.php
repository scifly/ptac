<?php

namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Participant, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use ReflectionException;

/**
 * Class ParticipantPolicy
 * @package App\Policies
 */
class ParticipantPolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Participant|null $participant
     * @return bool
     * @throws ReflectionException
     */
    function operation(User $user, Participant $participant = null) {
    
        if ($participant) {
            $perm = collect($this->contactIds('educator'))->has($participant->educator_id)
                && $participant->conference->room->building->school_id == $this->schoolId();
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}

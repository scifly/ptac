<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{PollTopic, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class PollTopicPolicy
 * @package App\Policies
 */
class PollTopicPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param PollTopic $topic
     * @return bool
     */
    public function operation(User $user, PollTopic $topic = null) {
    
        $perm = !$topic ? true : $topic->poll->school_id == $this->schoolId();
        
        return $this->action($user) && $perm;
        
    }
    
}

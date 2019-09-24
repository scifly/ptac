<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Poll, PollTopic, User};
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
    
        if ($pollId = $this->field('poll_id', $topic)) {
            $perm = Poll::find($pollId)->school_id == $this->schoolId();
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}

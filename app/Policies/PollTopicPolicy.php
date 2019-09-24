<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{Poll, PollTopic, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Auth;
use Request;

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
    
        [$pollId, $category] = array_map(
            function ($field) use ($topic) {
                return $this->field($field, $topic);
            }, ['poll_id', 'category']
        );
        if (isset($pollId, $category)) {
            $content = json_decode(Request::input('content'), true);
            if ($category && empty($content)) return false;
            $poll = Poll::find($pollId);
            $perm = $poll->school_id == $this->schoolId()
                && (in_array($user->role(), Constant::SUPER_ROLES) ? true : $poll->user_id == Auth::id());
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}

<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Message, PollReply, PollTopic, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class PollReplyPolicy
 * @package App\Policies
 */
class PollReplyPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param PollReply|null $reply
     * @return bool
     */
    function operation(User $user, PollReply $reply = null) {
        
        [$userId, $topicId] = array_map(
            function ($field) use ($reply) {
                return $this->field($field, $reply);
            }, ['user_id', 'poll_topic_id']
        );
        if (isset($userId, $topicId)) {
            $poll = PollTopic::find($topicId)->poll;
            $perm = $poll->school_id == $this->schoolId()
                && (new Message)->targetUserIds($poll->message)->flip()->has($userId);
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}

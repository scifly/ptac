<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Poll;
use App\Models\PollReply;
use App\Models\PollTopic;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class PollReplyPolicy
 * @package App\Policies
 */
class PollReplyPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    function __construct() { }
    
    /**
     * @param User $user
     * @param PollReply|null $reply
     * @return bool
     */
    function operation(User $user, PollReply $reply = null) {
        
        return true;
        
    }
    
}

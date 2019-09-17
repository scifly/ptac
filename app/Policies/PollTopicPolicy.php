<?php
namespace App\Policies;

use App\Helpers\{Constant, HttpStatusCode, ModelTrait, PolicyTrait};
use App\Models\{PollTopic, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class PollTopicPolicy
 * @package App\Policies
 */
class PollTopicPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     ** @return void
     */
    public function __construct() { }
    
    /**
     * @param User $user
     * @param PollTopic $topic
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, PollTopic $topic = null, $abort = false) {
    
        abort_if(
            $abort && !$topic,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') return true;
        $isSchoolAllowed = false;
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['show', 'edit', 'update', 'delete'])) {
            $isSchoolAllowed = in_array($topic->poll->school_id, $this->schoolIds());
        }
        if (in_array($action, ['index', 'create', 'store'])) {
            return $isSuperRole ? true : $this->action($user);
        } elseif (in_array($action, ['show', 'edit', 'update', 'delete'])) {
            return $isSuperRole
                ? $isSchoolAllowed
                : $this->action($user) && $isSchoolAllowed && ($topic->poll->user_id == $user->id);
        }
    
        return false;
        
    }
    
}

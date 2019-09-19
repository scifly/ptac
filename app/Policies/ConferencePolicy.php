<?php
namespace App\Policies;

use App\Helpers\{HttpStatusCode, ModelTrait, PolicyTrait};
use App\Models\{Conference, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ConferencePolicy
 * @package App\Policies
 */
class ConferencePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /** Create a new policy instance. */
    public function __construct() { }
    
    /**
     * Determine if the user can (e)dit / (u)pdate / (d)elete the conference
     *
     * @param User $user
     * @param Conference $conference
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Conference $conference = null, $abort = false) {
        
        abort_if(
            $abort && !$conference,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if (!$user->educator) return false;

        return $user->role() == '学校'
            ? (!$conference ? true : $conference->room->building->school_id != $this->schoolId())
            : (!$conference ? $this->action($user) : $this->action($user) && ($conference->user_id == $user->id));
            
    }
    
}

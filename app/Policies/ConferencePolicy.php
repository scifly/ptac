<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{Conference, School, User};
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
     */
    function operation(User $user, Conference $conference = null) {
        
        if (!$user->educator) return false;
        $perm = true;
        if ($roomId = $this->field('room_id', $conference)) {
            $perm &= School::find($this->schoolId())->rooms->pluck('id')->has($roomId);
        }
        if (!in_array($user->role(), Constant::SUPER_ROLES)) {
            $perm &= !$conference ? true : $conference->user_id == Auth::id();
        }
        // todo: message should be taken into consideration
        
        return $this->action($user) && $perm;
        
    }
    
}

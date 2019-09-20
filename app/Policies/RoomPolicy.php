<?php

namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Room, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class RoomTypePolicy
 * @package App\Policies
 */
class RoomPolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Determine whether the user can (e)dit/(u)pdate/sync (m)enu of the app
     *
     * @param User $user
     * @param Room|null $room
     * @return bool
     */
    function operation(User $user, Room $room = null) {

        if ($room) {
            $perm = $room->building->school_id == $this->schoolId()
                && $room->roomType->corp_id == $this->corpId();
        }
        
        return $this->action($user) && ($perm ?? true);
    
    }

}

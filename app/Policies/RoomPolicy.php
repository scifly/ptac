<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\{Room, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class RoomTypePolicy
 * @package App\Policies
 */
class RoomPolicy {

    use HandlesAuthorization, ModelTrait;
    
    /**
     * Determine whether the user can (e)dit/(u)pdate/sync (m)enu of the app
     *
     * @param User $user
     * @param Room|null $room
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Room $room = null, $abort = false) {

        abort_if(
            $abort && !$room,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') {
            return true;
        } elseif (in_array($role, ['企业', '学校'])) {
            return in_array(
                $room->building->school_id,
                $this->schoolIds()
            );
        } else {
            return false;
        }
    
    
    }

}

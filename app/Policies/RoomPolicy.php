<?php

namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Building, Room, RoomType, User};
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

        [$buildingId, $roomTypeId] = array_map(
            function ($field) use ($room) {
                return $this->field($field, $room);
            }, ['building_id', 'room_type_id']
        );
        if (isset($buildingId, $roomTypeId)) {
            $perm = Building::find($buildingId)->school_id == $this->schoolId()
                && RoomType::find($roomTypeId)->corp_id == $this->corpId();
        }
        
        return $this->action($user) && ($perm ?? true);
    
    }

}

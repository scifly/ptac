<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\{User, Bed};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class BedPolicy
 * @package App\Policies
 */
class BedPolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Determine whether the user can (e)dit/(u)pdate/sync (m)enu of the app
     *
     * @param User $user
     * @param Bed|null $bed
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Bed $bed = null, $abort = false) {

        abort_if(
            $abort && !$bed,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') {
            return true;
        } elseif (in_array($role, ['企业', '学校'])) {
            return in_array(
                $bed->room->building->school_id,
                $this->schoolIds()
            );
        } else {
            return $this->action($user);
        }
    
    
    }

}

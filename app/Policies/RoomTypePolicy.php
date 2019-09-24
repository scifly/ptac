<?php

namespace App\Policies;

use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\{RoomType, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class RoomTypePolicy
 * @package App\Policies
 */
class RoomTypePolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Determine whether the user can (e)dit/(u)pdate/sync (m)enu of the app
     *
     * @param User $user
     * @param RoomType|null $rt
     * @return bool
     */
    function operation(User $user, RoomType $rt = null) {

        if ($corpId = $this->field('corp_id', $rt)) {
            $perm = $corpId == $this->corpId();
        }

        return in_array($user->role(), ['运营', '企业']) && ($perm ?? true);
    
    }

}

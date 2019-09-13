<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\{RoomType, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class RoomTypePolicy
 * @package App\Policies
 */
class RoomTypePolicy {

    use HandlesAuthorization, ModelTrait;
    
    /**
     * Determine whether the user can (e)dit/(u)pdate/sync (m)enu of the app
     *
     * @param User $user
     * @param RoomType|null $rt
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, RoomType $rt = null, $abort = false) {

        abort_if(
            $abort && !$rt,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') {
            return true;
        } elseif ($role == '企业') {
            return explode('/', Request::path())[1] != 'index'
                ? $this->corpId() == $rt->corp_id : true;
        } else {
            return false;
        }
    
    
    }

}

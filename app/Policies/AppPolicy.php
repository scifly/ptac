<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\{App, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AppPolicy
 * @package App\Policies
 */
class AppPolicy {

    use HandlesAuthorization, ModelTrait;
    
    protected $menu;
    
    /**
     * Determine whether the user can (e)dit/(u)pdate/sync (m)enu of the app
     *
     * @param User $user
     * @param App $app
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, App $app = null, $abort = false) {
    
        abort_if(
            $abort && !$app,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') {
            return true;
        } elseif ($role == '企业') {
            return !$app ? true : $app->corp_id == $this->corpId();
        } else {
            return false;
        }

    }

}

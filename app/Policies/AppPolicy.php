<?php

namespace App\Policies;

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
     * @return bool
     */
    function operation(User $user, App $app = null) {
    
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

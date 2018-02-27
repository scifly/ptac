<?php

namespace App\Policies;

use App\Models\App;
use App\Models\Corp;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppPolicy {

    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }

    /**
     * Determine whether the user can (e)dit/(u)pdate/sync (m)enu of the app
     *
     * @param User $user
     * @param App $app
     * @return bool
     */
    public function eum(User $user, App $app) {

<<<<<<< HEAD
        if (!$app) { abort(404); }
=======
        abort_if(!$app, HttpStatusCode::NOT_FOUND, __('messages.not_found'));
>>>>>>> a8b77c532a4d09f2fe4f9feaadd84ba5d5a4fd12
        $role = $user->group->name;
        switch ($role) {
            case '运营': return true;
            case '企业':
                $userCorp = Corp::whereDepartmentId($user->topDeptId())->first();
                return $userCorp->id == $app->corp_id;
            default: return false;
        }

    }

}

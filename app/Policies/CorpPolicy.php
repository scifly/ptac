<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Corp;
use Illuminate\Auth\Access\HandlesAuthorization;

class CorpPolicy {

    use HandlesAuthorization;

    /**
     * Determine whether the user can create a corp
     *
     * @param User $user
     * @return bool
     */
    public function create(User $user) {

        return $user->group->name == '运营';

    }

    /**
     * Determine whether the user can view/edit/update/delete the corp.
     *
     * @param  User $user
     * @param  Corp $corp
     * @return bool
     */
    public function veud(User $user, Corp $corp) {

        if (!$corp) { abort(404); }
        $role = $user->group->name;
        switch ($role) {
            case '运营': return true;
            case '企业':
                # userCorp - the Corp to which user belongs
                $userCorp = Corp::whereDepartmentId($user->topDeptId())->first();
                return $userCorp->id == $corp->id;
            default: return false;
        }

    }

}

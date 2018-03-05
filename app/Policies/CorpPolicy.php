<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
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
     * Determine whether the user can (v)iew/(e)dit/(u)pdate/(d)elete the corp.
     *
     * @param  User $user
     * @param  Corp $corp
     * @return bool
     */
    public function veud(User $user, Corp $corp) {

        abort_if(
            !$corp,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        switch ($role) {
            case '运营': return true;
            case '企业':
                # userCorp - the Corp to which the user belongs
                $userCorp = Corp::whereDepartmentId($user->topDeptId())->first();
                return $userCorp->id == $corp->id;
            default: return false;
        }

    }

}

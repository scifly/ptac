<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class CorpPolicy
 * @package App\Policies
 */
class CorpPolicy {

    use HandlesAuthorization;
    
    /**
     * @param  User $user
     * @return bool
     */
    function operation(User $user) {

        return $user->role() == '运营';

    }

}

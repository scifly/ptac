<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\Corp;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class CorpPolicy
 * @package App\Policies
 */
class CorpPolicy {

    use HandlesAuthorization;
    
    function __construct() { }
    
    /**
     * Determine whether the user can (v)iew/(e)dit/(u)pdate/(d)elete the corp.
     *
     * @param  User $user
     * @param  Corp $corp
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Corp $corp = null, $abort = false) {

        abort_if(
            $abort && !$corp,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->role() == '运营';

    }

}

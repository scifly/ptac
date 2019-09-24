<?php

namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{App, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class AppPolicy
 * @package App\Policies
 */
class AppPolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param App $app
     * @return bool
     */
    function operation(User $user, App $app = null) {
    
        $corpId = $this->field('corp_id', $app);
        if (isset($corpId) && $user->role() == '企业') {
            $perm = $corpId == $this->corpId();
        }
        
        
        return in_array($user->role(), ['运营', '企业']) && ($perm ?? true);
        
    }

}

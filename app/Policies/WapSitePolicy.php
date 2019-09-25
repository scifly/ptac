<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{User, WapSite};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class WapSitePolicy
 * @package App\Policies
 */
class WapSitePolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * @param User $user
     * @param WapSite|null $ws
     * @return bool
     */
    function operation(User $user, WapSite $ws = null) {
        
        if ($schoolId = $this->field('school_id', $ws)) {
            $perm = collect($this->schoolIds())->flip()->has($schoolId);
        }
        
        return in_array($user->role(), Constant::SUPER_ROLES) && ($perm ?? true);
        
    }
    
}

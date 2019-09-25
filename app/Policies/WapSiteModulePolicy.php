<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{User, WapSite, WapSiteModule};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class WapSiteModulePolicy
 * @package App\Policies
 */
class WapSiteModulePolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param WapSiteModule $wsm
     * @return bool
     */
    function operation(User $user, WapSiteModule $wsm = null) {
        
        if ($wsId = $this->field('wap_site_id', $wsm)) {
            $perm = collect($this->schoolIds())->flip()->has(WapSite::find($wsId)->school_id);
        }
        
        return in_array($user->role(), Constant::SUPER_ROLES) && ($perm ?? true);
        
    }
    
}

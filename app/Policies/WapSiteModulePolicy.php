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
     * @param User $user
     * @param WapSiteModule $wsm
     * @return bool
     */
    function operation(User $user, WapSiteModule $wsm = null) {
        
        $perm = true;
        [$wsId, $ids] = array_map(
            function ($field) use ($wsm) {
                return $this->field($field, $wsm);
            }, ['wap_site_id', 'ids']
        );
        $schoolIds = $this->schoolIds()->flip();
        !$wsId ?: $perm &= $schoolIds->has(WapSite::find($wsId)->school_id);
        !$ids ?: $perm &= WapSite::whereSchoolId($this->schoolId())->first()
                ->modules->pluck('id')->flip()->has(array_values($ids));
        
        return in_array($user->role(), Constant::SUPER_ROLES) && $perm;
        
    }
    
}

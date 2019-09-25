<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{User, WapSiteModule, WsmArticle};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class WsmArticlePolicy
 * @package App\Policies
 */
class WsmArticlePolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param WsmArticle|null $wsma
     * @return bool
     */
    function operation(User $user, WsmArticle $wsma = null) {
    
        if ($wsmId = $this->field('wsm_id', $wsma)) {
            $perm = collect($this->schoolIds())->flip()->has(
                WapSiteModule::find($wsmId)->wapsite->school_id
            );
        }
    
        return in_array($user->role(), Constant::SUPER_ROLES) && ($perm ?? true);
        
    }
    
}

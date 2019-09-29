<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{User, WapSite, WapSiteModule, WsmArticle};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class WsmArticlePolicy
 * @package App\Policies
 */
class WsmArticlePolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * @param User $user
     * @param WsmArticle|null $wsma
     * @return bool
     */
    function operation(User $user, WsmArticle $wsma = null) {
    
        $perm = true;
        $schoolIds = $this->schoolIds()->flip();
        [$wsmId, $ids] = array_map(
            function ($field) use ($wsma) {
                return $this->field($field, $wsma);
            }, ['wsm_id', 'ids']
        );
        !$wsmId ?: $perm &= $schoolIds->has(
            WapSiteModule::find($wsmId)->wapsite->school_id
        );
        !$ids ?: $perm &= WapSite::whereSchoolId($this->schoolId())
            ->articles->pluck('id')->flip()->has(array_values($ids));
    
        return in_array($user->role(), Constant::SUPER_ROLES) && ($perm ?? true);
        
    }
    
}

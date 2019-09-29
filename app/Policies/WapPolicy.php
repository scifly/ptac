<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{User, Wap};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class WapPolicy
 * @package App\Policies
 */
class WapPolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * @param User $user
     * @param Wap|null $ws
     * @return bool
     */
    function operation(User $user, Wap $ws = null) {
        
        if ($schoolId = $this->field('school_id', $ws)) {
            $perm = $this->schoolIds()->flip()->has($schoolId);
        }
        
        return in_array($user->role(), Constant::SUPER_ROLES) && ($perm ?? true);
        
    }
    
}

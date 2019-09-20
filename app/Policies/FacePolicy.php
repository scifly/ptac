<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Face, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class FacePolicy
 * @package App\Policies
 */
class FacePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Face|null $face
     * @return bool
     * @throws Exception
     */
    function operation(User $user, Face $face = null) {
    
        $perm = !$face ? true : in_array($face->user_id, explode(',', $this->visibleUserIds()));
    
        return $this->action($user) && $perm;
        
    }
    
}
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
     * @param User $user
     * @param Face|null $face
     * @return bool
     * @throws Exception
     */
    function operation(User $user, Face $face = null) {
    
        $perm = true;
        $userIds = collect(explode(',', $this->visibleUserIds()))->flip();
        [$userId, $ids] = array_map(
            function ($field) use ($face) {
                return $this->field($field, $face);
            }, ['user_id', 'ids']
        );
        !$userId ?: $perm &= $userIds->has($userId);
        !$ids ?: $perm &= $userIds->has(array_values($ids));
    
        return $this->action($user) && $perm;
        
    }
    
}
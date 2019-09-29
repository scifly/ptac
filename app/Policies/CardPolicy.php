<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Card, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;
/**
 * Class CardPolicy
 * @package App\Policies
 */
class CardPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Card|null $card
     * @return bool
     * @throws Exception
     */
    function operation(User $user, Card $card = null) {
    
        $perm = true;
        $userIds = collect(explode(',', $this->visibleUserIds()))->flip();
        [$userId, $ids] = array_map(
            function ($field) use ($card) {
                return $this->field($field, $card);
            }, ['user_id', 'ids']
        );
        !$userId ?: $perm &= $userIds->has($userId);
        !$ids ?: $perm &= $userIds->has(array_values($ids));
        
        return $this->action($user) && $perm;
        
    }
    
}
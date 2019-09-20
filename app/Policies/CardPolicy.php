<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Card, User};
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
     */
    function operation(User $user, Card $card = null) {
        
        if ($userId = $this->field('user_id', $card)) {
            $perm = collect(explode(',', $this->visibleUserIds()))->has($userId);
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}
<?php
namespace App\Policies;

use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ScoreTotalPolicy
 * @package App\Policies
 */
class ScoreTotalPolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * @param User $user
     * @return bool
     */
    function operation(User $user) {
        
        return $this->action($user);
        
    }
    
}

<?php

namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class MethodPolicy
 * @package App\Policies
 */
class MethodPolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * @param User $user
     * @param Route $route
     * @return bool
     */
    public function act(User $user, Route $route) {
    
        return $this->action($user, $route->uri);

    }
    
}

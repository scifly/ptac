<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Menu, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use ReflectionException;

/**
 * Class MenuPolicy
 * @package App\Policies
 */
class MenuPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Menu $menu
     * @return bool
     * @throws ReflectionException
     */
    public function operation(User $user, Menu $menu = null) {
        
        return $this->action($user) && (!$menu ? true : in_array($menu->id, $this->menuIds()));
        
    }
    
}

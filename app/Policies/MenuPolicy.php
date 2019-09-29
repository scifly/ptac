<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Menu, MenuType, User};
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
    
        $perm = true;
        $menuIds = $this->menuIds()->flip();
        if ($parentId = $this->field('parent_id', $menu)) {
            $perm &= $menuIds->has(
                $menu ? array_values([$menu->id, $parentId]) : $parentId
            );
        } elseif ($menu) {
            $perm &= $menuIds->has($menu->id);
        }
    
        return $this->action($user) && $perm;
        
    }
    
}

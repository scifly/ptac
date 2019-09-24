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
    
        [$parentId, $mTypeId] = array_map(
            function ($field) use ($menu) {
                return $this->field($field, $menu);
            }, ['parent_id', 'menu_type_id']
        );
        if (isset($parentId, $mTypeId)) {
            $perm = collect($this->menuIds())->has($menu ? [$menu->id, $parentId] : $parentId)
                && MenuType::find($mTypeId)->name == '其他';
        }
    
        return $this->action($user) && ($perm ?? true);
        
    }
    
}

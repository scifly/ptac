<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Department, User};
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class DepartmentPolicy
 * @package App\Policies
 */
class DepartmentPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Department $dept
     * @return bool
     */
    function operation(User $user, Department $dept = null) {
        
        $perm = !$dept ? true : in_array($dept->id, $this->departmentIds());
        
        return $this->action($user) && $perm;
    
    }
    
}

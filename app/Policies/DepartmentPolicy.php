<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Department, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class DepartmentPolicy
 * @package App\Policies
 */
class DepartmentPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Department $dept
     * @return bool
     * @throws Exception
     */
    function operation(User $user, Department $dept = null) {
        
        $perm = true;
        $deptIds = $this->departmentIds()->flip();
        if ($parentId = $this->field('parent_id', $dept)) {
            $perm &= $deptIds->has(
                $dept ? array_values([$dept->id, $parentId]) : $parentId
            );
        } elseif ($dept) {
            $perm &= $deptIds->has($dept->id);
        }
        
        return $this->action($user) && $perm;
    
    }
    
}

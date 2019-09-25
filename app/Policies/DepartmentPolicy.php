<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Department, DepartmentType, User};
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
        
        [$parentId, $dTypeId] = array_map(
            function ($field) use ($dept) {
                return $this->field($field, $dept);
            }, ['parent_id', 'department_type_id']
        );
        if (isset($parentId, $dTypeId)) {
            $perm = collect($this->departmentIds())->flip()->has($dept ? [$dept->id, $parentId] : $parentId)
                && DepartmentType::find($dTypeId)->name == '其他';
        }
        
        return $this->action($user) && ($perm ?? true);
    
    }
    
}

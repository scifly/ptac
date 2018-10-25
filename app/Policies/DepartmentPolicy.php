<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class DepartmentPolicy
 * @package App\Policies
 */
class DepartmentPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Department $department
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Department $department = null, $abort = false) {
        
        abort_if(
            $abort && !$department,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['index', 'create', 'store'])) {
            return $isSuperRole ? true : $this->action($user);
        }
        if (in_array($action, ['edit', 'update', 'delete'])) {
            $isDepartmentAllowed = in_array($department->id, $this->departmentIds($user->id));
            return $isSuperRole ? $isDepartmentAllowed : ($isDepartmentAllowed && $this->action($user));
        }
        
        return false;
    
    }
    
}

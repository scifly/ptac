<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

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
     * Determine whether the current user can (c)reate / (s)tore a Department,
     * and (s)ort departments
     *
     * @param User $user
     * @return bool
     */
    public function css(User $user) {
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
    
        return $this->action($user);
        
    }
    
    /**
     * Determine whether the current user can (s)how / (e)dit / (u)pdate / (d)estroy a Department
     *
     * @param User $user
     * @param Department $department
     * @return bool
     */
    public function seud(User $user, Department $department) {
        
        abort_if(
            !$department,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
    
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        return $this->action($user) && in_array($department->id, $this->departmentIds($user->id));
    
    }
    
}

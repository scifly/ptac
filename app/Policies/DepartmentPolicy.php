<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Department;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
    
    public function create(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    public function store(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    public function sort(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    /**
     * Determine whether the current user can (c)reate / (s)tore / (s)ort Department(s),
     * and (s)ort departments
     *
     * @param User $user
     * @return bool
     */
    private function classPerm(User $user) {
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
    
        return $this->action($user);
        
    }
    
    public function show(User $user, Department $department) {
        
        return $this->objectPerm($user, $department);
        
    }
    
    public function edit(User $user, Department $department) {
        
        return $this->objectPerm($user, $department);
        
    }
    
    public function update(User $user, Department $department) {
        
        return $this->objectPerm($user, $department);
        
    }
    
    public function destroy(User $user, Department $department) {
        
        return $this->objectPerm($user, $department);
        
    }
    
    /**
     * Determine whether the current user can (s)how / (e)dit / (u)pdate / (d)estroy a Department
     *
     * @param User $user
     * @param Department $department
     * @return bool
     */
    private function objectPerm(User $user, Department $department) {
        
        abort_if(
            !$department,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
    
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        return $this->action($user)
            && in_array($department->id, $this->departmentIds($user->id));
    
    }
    
}

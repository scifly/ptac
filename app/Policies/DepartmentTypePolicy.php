<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Models\DepartmentType;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DepartmentTypePolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    function create(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    function store(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    private function classPerm(User $user) {
        
        return $user->group->name == '运营';
        
    }
    
    function edit(User $user, DepartmentType $dt) {
        
        return $this->objectPerm($user, $dt);
        
    }
    
    function update(User $user, DepartmentType $dt) {
        
        return $this->objectPerm($user, $dt);
        
    }
    
    function destroy(User $user, DepartmentType $dt) {
        
        return $this->objectPerm($user, $dt);
        
    }
    
    private function objectPerm(User $user, DepartmentType $dt) {
        
        abort_if(
            !$dt,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $user->group->name == '运营';
        
    }
    
}
<?php

namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Action;
use App\Models\ActionGroup;
use App\Models\Custodian;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class CustodianPolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    function create(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    function store(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    function export(User $user) {
        
        return $this->classPerm($user);
        
    }
    
    /**
     * Determine whether the current user can (c)reate / (s)tore / (e)xport Custodians
     *
     * @param User $user
     * @return bool
     */
    private function classPerm(User $user) {
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) { return true; }
        
        return $this->action($user);
        
    }
    
    public function show(User $user, Custodian $custodian) {
        
        return $this->objectPerm($user, $custodian);
        
    }
    
    public function edit(User $user, Custodian $custodian) {
        
        return $this->objectPerm($user, $custodian);
        
    }
    
    public function update(User $user, Custodian $custodian) {
        
        return $this->objectPerm($user, $custodian);
        
    }
    
    public function destroy(User $user, Custodian $custodian) {
        
        return $this->objectPerm($user, $custodian);
        
    }
    
    /**
     * Determine whether the current user can (s)how / (e)dit / (u)pdate / (d)estory a Custodian
     *
     * @param User $user
     * @param Custodian $custodian
     * @return bool
     */
    private function objectPerm(User $user, Custodian $custodian) {
    
        abort_if(
            !$custodian,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if (in_array($user->group->name, Constant::SUPER_ROLES)) { return true; }
        
        return in_array($custodian->id, $this->contactIds('custodian'))
            && $this->action($user);
        
    }

}

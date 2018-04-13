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
    
    /**
     * Determine whether the current user can (c)reate / (s)tore / (e)xport Custodians
     *
     * @param User $user
     * @return bool
     */
    function cse(User $user) {
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) { return true; }
        $actionId = Action::whereRoute(trim(Request::route()->uri()))->first()->id;
        $ag = ActionGroup::whereGroupId($user->group_id)->where('action_id', $actionId)->first();
    
        return $ag ? true : false;
        
    }
    
    /**
     * Determine whether the current user can (s)how / (e)dit / (u)pdate / (d)estory a Custodian
     *
     * @param User $user
     * @param Custodian $custodian
     * @return bool
     */
    public function seud(User $user, Custodian $custodian) {
    
        abort_if(
            !$custodian,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if (in_array($user->group->name, Constant::SUPER_ROLES)) { return true; }
        
        return in_array($custodian->id, $this->contactIds('custodian')) && $this->action($user);
        
    }

}

<?php

namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Custodian, Group, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;
use ReflectionException;

/**
 * Class CustodianPolicy
 * @package App\Policies
 */
class CustodianPolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Custodian $custodian
     * @return bool
     * @throws ReflectionException
     */
    function operation(User $user, Custodian $custodian = null) {
    
        $perm = true;
        if (!$ids = Request::input('ids')) {
            $groupId = Request::input('user')['group_id'] ?? ($custodian ? $custodian->user->group_id : null);
            !$groupId ?: $perm &= $groupId == Group::whereName('监护人')->first()->id;
            $studentIds = Request::input('student_ids')
                ?? ($custodian ? $custodian->students->pluck('id') : null);
            !$studentIds ?: $perm &= collect($this->contactIds('student'))->flip()->has($studentIds);
        } else {
            $perm &= collect($this->contactIds('custodian'))->flip()->has(array_values($ids));
        }
        
        return $this->action($user) && $perm;
        
    }

}

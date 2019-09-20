<?php

namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Custodian, User};
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
    
        $studentIds = Request::input('student_ids')
            ?? ($custodian ? $custodian->students->pluck('id') : null);
        if ($studentIds) {
            $perm = collect($this->contactIds('student'))->has($studentIds);
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }

}

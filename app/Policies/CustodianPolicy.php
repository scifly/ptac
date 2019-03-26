<?php

namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Custodian;
use App\Models\User;
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
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * Determine whether the current user can (s)how / (e)dit / (u)pdate / (d)estory a Custodian
     *
     * @param User $user
     * @param Custodian $custodian
     * @param bool $abort
     * @return bool
     * @throws ReflectionException
     */
    function operation(User $user, Custodian $custodian = null, $abort = false) {
    
        abort_if(
            $abort && !$custodian,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $action = explode('/', Request::path())[1];
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $isStudentAllowed = $isCustodianAllowed = false;
        if (in_array($action, ['store', 'update'])) {
            $studentIds = Request::input('student_ids');
            $isStudentAllowed = empty(array_diff($studentIds, $this->contactIds('student')));
        }
        if (in_array($action, ['show', 'edit', 'delete', 'update'])) {
            $isCustodianAllowed = in_array($custodian->id, $this->contactIds('custodian'));
        }
        switch ($action) {
            case 'index':
            case 'create':
            case 'export':
            case 'issue':
            case 'permit':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole ? $isStudentAllowed : ($isStudentAllowed && $this->action($user));
            case 'show':
            case 'edit':
            case 'delete':
                return $isSuperRole ? $isCustodianAllowed : ($isCustodianAllowed && $this->action($user));
            case 'update':
                return $isSuperRole
                    ? ($isStudentAllowed && $isCustodianAllowed)
                    : ($isStudentAllowed && $isCustodianAllowed && $this->action($user));
            default:
                return false;
        }
        
    }

}

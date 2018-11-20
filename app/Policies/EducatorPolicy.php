<?php

namespace App\Policies;

use App\Models\{User, Group, Educator};
use App\Helpers\{Constant, ModelTrait, PolicyTrait, HttpStatusCode};
use Illuminate\Support\Facades\Request;
use Illuminate\Auth\Access\HandlesAuthorization;
use ReflectionException;

/**
 * Class EducatorPolicy
 * @package App\Policies
 */
class EducatorPolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Educator|null $educator
     * @param bool $abort
     * @return bool
     * @throws ReflectionException
     */
    function operation(User $user, Educator $educator = null, $abort = false) {
    
        abort_if(
            $abort && !$educator,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') return true;
        $action = explode('/', Request::path())[1];
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $isGroupAllowed = $isDepartmentAllowed = $isEducatorAllowed = false;
        
        if (in_array($action, ['store', 'update'])) {
            $groupId = Request::input('user')['group_id'];
            $selectedDepartmentIds = Request::input('selectedDepartments');
            switch ($role) {
                case '企业':
                    $allowedGroupIds = Group::whereEnabled(1)
                        ->whereIn('name', ['企业', '学校'])
                        ->orWhereIn('school_id', $this->schoolIds())
                        ->pluck('id')->toArray();
                    break;
                case '学校':
                    $allowedGroupIds = Group::whereEnabled(1)
                        ->where('name', '学校')
                        ->orWhere('school_id', $this->schoolId())
                        ->pluck('id')->toArray();
                    break;
                default:
                    $allowedGroupIds = array_unique(
                        Group::whereEnabled(1)
                            ->where('id', $user->group->id)
                            ->orWhere('name', '教职员工')
                            ->pluck('id')->toArray()
                    );
                    break;
            }
            $isDepartmentAllowed = empty(array_diff(
                $selectedDepartmentIds, $this->departmentIds($user->id))
            );
            $isGroupAllowed = in_array($groupId, $allowedGroupIds);
        }
        
        if (in_array($action, ['show', 'edit', 'update', 'delete', 'recharge'])) {
            $isEducatorAllowed = in_array($educator->id, $this->contactIds('educator'));
        }
        
        switch ($action) {
            case 'index':
            case 'create':
            case 'import':
            case 'export':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole
                    ? ($isGroupAllowed && $isDepartmentAllowed)
                    : ($isGroupAllowed && $isDepartmentAllowed && $this->action($user));
            case 'show':
            case 'edit':
            case 'destroy':
            case 'delete':
                return $isSuperRole ? $isEducatorAllowed : ($isEducatorAllowed && $this->action($user));
            case 'update':
                return $isSuperRole
                    ? ($isGroupAllowed && $isEducatorAllowed && $isDepartmentAllowed)
                    : ($isGroupAllowed && $isEducatorAllowed && $isDepartmentAllowed && $this->action($user));
            default:
                return false;
        }
    
    }

}
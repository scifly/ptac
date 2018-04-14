<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Educator;
use App\Models\Group;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class EducatorPolicy {

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
    
    function import(User $user) {
    
        return $this->classPerm($user);
        
    }
    
    function export(User $user) {
    
        return $this->classPerm($user);
        
    }
    
    private function classPerm(User $user) {
    
        switch ($user->group->name) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return in_array($this->schoolId(), $this->schoolIds());
            default:
                return in_array($this->schoolId(), $this->schoolIds()) && $this->action($user);
        }
        
    }
    
    function store(User $user) {
        
        $schoolId = Request::input('educator')['school_id'];
        $groupId = Request::input('educator')['group_id'];
        $selectedDepartmentIds = Request::input('selectedDepartments');
        switch ($user->group->name) {
            case '运营':
                return true;
            case '企业':
                $allowedGroupIds = array_merge(
                    Group::whereEnabled(1)->whereIn('name', ['企业', '学校'])->get()->pluck('id')->toArray(),
                    Group::whereEnabled(1)->whereIn('schoolId', $this->schoolIds())
                );
                return in_array($schoolId, $this->schoolIds())
                    && in_array($groupId, $allowedGroupIds)
                    && empty(array_diff($selectedDepartmentIds, $this->departmentIds($user->id)));
            case '学校':
                $allowedGroupIds = array_merge(
                    [Group::whereName('学校')->first()->id],
                    Group::whereEnabled(1)->where('school_id', $this->schoolId())->get()->pluck('id')->toArray()
                );
                return $schoolId == $this->schoolId()
                    && in_array($groupId, $allowedGroupIds)
                    && empty(array_diff($selectedDepartmentIds, $this->departmentIds($user->id)));
            default:
                $allowedGroupIds = array_unique([
                    $user->group->id,
                    Group::whereName('教职员工')->first()->id
                ]);
                return $schoolId == $this->schoolId()
                    && in_array($groupId, $allowedGroupIds)
                    && empty(array_diff($selectedDepartmentIds, $this->departmentIds($user->id)))
                    && $this->action($user);
        }
        
    }
    
    function edit(User $user, Educator $educator) {
        
        return $this->objectPerm($user, $educator);
        
    }
    
    function show(User $user, Educator $educator) {
        
        return $this->objectPerm($user, $educator);
        
    }
    
    function destroy(User $user, Educator $educator) {
        
        return $this->objectPerm($user, $educator);
        
    }
    
    function recharge(User $user, Educator $educator) {
        
        return $this->objectPerm($user, $educator);
        
    }
    
    function rechargeStore(User $user, Educator $educator) {
        
        return $this->objectPerm($user, $educator);
        
    }
    
    function update(User $user, Educator $educator) {
    
        abort_if(
            !$educator,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $schoolId = Request::input('educator')['school_id'];
        $groupId = Request::input('educator')['group_id'];
        $selectedDepartmentIds = Request::input('selectedDepartments');
        switch ($user->group->name) {
            case '运营':
                return true;
            case '企业':
                $allowedGroupIds = array_merge(
                    Group::whereEnabled(1)->whereIn('name', ['企业', '学校'])->get()->pluck('id')->toArray(),
                    Group::whereEnabled(1)->whereIn('schoolId', $this->schoolIds())
                );
                return in_array($schoolId, $this->schoolIds())
                    && in_array($groupId, $allowedGroupIds)
                    && empty(array_diff($selectedDepartmentIds, $this->departmentIds($user->id)));
            case '学校':
                $allowedGroupIds = array_merge(
                    [Group::whereName('学校')->first()->id],
                    Group::whereEnabled(1)->where('school_id', $this->schoolId())->get()->pluck('id')->toArray()
                );
                return $schoolId == $this->schoolId()
                    && in_array($groupId, $allowedGroupIds)
                    && empty(array_diff($selectedDepartmentIds, $this->departmentIds($user->id)));
            default:
                $allowedGroupIds = array_unique([
                    $user->group->id,
                    Group::whereName('教职员工')->first()->id
                ]);
                return $schoolId == $this->schoolId()
                    && in_array($groupId, $allowedGroupIds)
                    && empty(array_diff($selectedDepartmentIds, $this->departmentIds($user->id)))
                    && $this->action($user);
        }
        
    }
    
    private function objectPerm(User $user, Educator $educator) {
    
        abort_if(
            !$educator,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        switch ($user->group->name) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return in_array($educator->id, $this->contactIds('educator'));
            default:
                return in_array($educator->id, $this->contactIds('educator'))
                    && $this->action($user);
        }
    
    }

}
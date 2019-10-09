<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{DepartmentUser, Educator, Group, Tag, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class EducatorPolicy
 * @package App\Policies
 */
class EducatorPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Educator|null $educator
     * @return bool
     * @throws Exception
     */
    function operation(User $user, Educator $educator = null) {
        
        // todo: 导出范围检查
        $perm = true;
        if (!$ids = Request::input('ids')) {
            $deptIds = Request::input('selectedDepartments');
            if (!$deptIds && $educator) {
                $deptIds = (new DepartmentUser)->where(
                    ['enabled' => 1, 'user_id' => $educator->user_id]
                )->pluck('department_id');
            }
            $groupId = Request::input('user')['group_id'] ?? ($educator ? $educator->user->group_id : null);
            if (isset($deptIds, $groupId)) {
                $perm &= collect($this->departmentIds())->flip()->has($deptIds)
                    && (new Group)->list()->keys()->flip()->has($groupId);
            }
            if ($tagIds = Request::input('tag_ids')) {
                $perm &= Tag::whereSchoolId($this->schoolId())->pluck('id')->flip()->has($tagIds);
            }
        } else {
            $perm &= $this->contactIds('educator')->flip()->has(array_values($ids));
        }
        
        return $this->action($user) && $perm;
        
    }
    
}
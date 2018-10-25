<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\Corp;
use App\Models\DepartmentUser;
use App\Models\Group;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class OperatorPolicy
 * @package App\Policies
 */
class OperatorPolicy {
    
    use HandlesAuthorization, ModelTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param User|null $operator
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, User $operator = null, $abort = false) {
        
        abort_if(
            $abort && !$operator,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $action = explode('/', Request::path())[1];
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $isOperatorAllowed = $isGroupAllowed = $isCorpAllowed = $isSchoolAllowed = true;
    
        # 对当前用户可见的超级用户ids
        $allowedOperatorIds = [$user->id];
        $departmentIds = School::whereIn('id', $this->schoolIds())->get()->pluck('department_id')->toArray();
        $userIds = DepartmentUser::whereIn('department_id', $departmentIds)->get()->pluck('user_id')->toArray();
        $allowedOperatorIds = array_merge($allowedOperatorIds, $userIds);
    
        # 批量操作
        if (Request::has('ids')) {
            $operatorIds = Request::input('ids');
            $isOperatorAllowed = empty(array_diff($operatorIds, $allowedOperatorIds));
        } else {
            if (in_array($action, ['store', 'update'])) {
                $corpGroupId = Group::whereName('企业')->first()->id;
                $schoolGroupId = Group::whereName('学校')->first()->id;
                $groupId = Request::input('group_id');
                $corpId = Request::input('corp_id');
                $schoolId = Request::input('school_id');
                $isGroupAllowed = $role == '企业'
                    ? in_array($groupId, [$corpGroupId, $schoolGroupId])
                    : $groupId == $schoolGroupId;
                if ($corpId) {
                    $departmentId = $this->head($user);
                    if ($role == '企业') {
                        $corp = Corp::whereDepartmentId($departmentId)->first();
                    } else {
                        $corp = School::whereDepartmentId($departmentId)->first()->corp;
                    }
                    $isCorpAllowed = $corpId == $corp->id;
                }
                if ($schoolId) {
                    $isSchoolAllowed = in_array($schoolId, $this->schoolIds());
                }
            }
            if (in_array($action, ['edit', 'update', 'delete'])) {
                $isOperatorAllowed = in_array($operator->id, $allowedOperatorIds);
            }
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperRole;
            case 'edit':
            case 'delete':
                return $isSuperRole && $isOperatorAllowed;
            case 'store':
                return $isSuperRole && $isGroupAllowed && $isCorpAllowed && $isSchoolAllowed;
            case 'update':
                if (Request::has('ids')) {
                    return $isSuperRole && $isOperatorAllowed;
                }
                return $isSuperRole && $isGroupAllowed && $isCorpAllowed && $isSchoolAllowed;
            default:
                return false;
                
        }
        
    }
    
}

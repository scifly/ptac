<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{App, School, SchoolType, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;
use ReflectionException;

/**
 * Class SchoolPolicy
 * @package App\Policies
 */
class SchoolPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param School $school
     * @return bool
     * @throws ReflectionException
     * @throws Exception
     */
    public function operation(User $user, School $school = null) {
        
        $perm = true;
        [$schoolTypeId, $corpId, $menuId, $deptId, $appId] = array_map(
            function ($field) use ($school) {
                return $this->field($field, $school);
            }, ['school_type_id', 'corp_id', 'menu_id', 'department_id', 'app_id']
        );
        if (isset($schoolTypeId, $corpId)) {
            $perm &= SchoolType::pluck('id')->flip()->has($schoolTypeId)
                && collect($user->corpIds())->flip()->has($corpId);
        }
        empty($menuId) ?: $perm &= collect($this->menuIds())->flip()->has($menuId);
        empty($deptId) ?: $perm &= collect($this->departmentIds())->flip()->has($deptId);
        empty($appId) ?: $perm &= collect($user->corpIds())->flip()->has(App::find($appId)->corp_id);
        
        return in_array($user->role(), Constant::SUPER_ROLES) && $perm;
        
    }
    
}
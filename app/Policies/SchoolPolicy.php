<?php
namespace App\Policies;

use App\Helpers\{Constant, ModelTrait, PolicyTrait};
use App\Models\{App, Corp, School, SchoolType, User};
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
        [$schoolTypeId, $corpId, $menuId, $deptId, $appId, $ids] = array_map(
            function ($field) use ($school) {
                return $this->field($field, $school);
            }, ['school_type_id', 'corp_id', 'menu_id', 'department_id', 'app_id', 'ids']
        );
        $corpIds = $user->corpIds()->flip();
        if (isset($schoolTypeId, $corpId)) {
            $perm &= SchoolType::pluck('id')->flip()->has($schoolTypeId) && $corpIds->has($corpId);
        }
        empty($menuId) ?: $perm &= $this->menuIds()->flip()->has($menuId);
        empty($deptId) ?: $perm &= $this->departmentIds()->flip()->has($deptId);
        empty($appId) ?: $perm &= $corpIds->has(App::find($appId)->corp_id);
        !$ids ?: $perm &= Corp::find($this->corpId())->schools
            ->pluck('id')->flip()->has(array_values($ids));
        
        return in_array($user->role(), Constant::SUPER_ROLES) && $perm;
        
    }
    
}
<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Squad, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;
use ReflectionException;

/**
 * Class SquadPolicy
 * @package App\Policies
 */
class SquadPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Squad|null $class
     * @return bool
     * @throws ReflectionException
     * @throws Exception
     */
    public function operation(User $user, Squad $class = null) {
        
        $perm = !$class ? true : collect($this->classIds())->flip()->has($class->id);
        [$gradeId, $educatorIds, $deptId] = array_map(
            function ($field) use ($class) {
                return Request::input($field)
                    ?? ($class ? explode(',', $class->{$field}) : null);
            }, ['grade_id', 'educator_ids', 'department_id']
        );
        if (isset($gradeId, $educatorIds)) {
            $perm &= collect($this->gradeIds())->flip()->has($gradeId) &&
                collect($this->contactIds('educator'))->flip()->has($educatorIds);
        }
        empty($deptId) ?: $perm &= collect($this->departmentIds())->flip()->has($deptId);
        
        return $this->action($user) && $perm;
        
    }
    
}

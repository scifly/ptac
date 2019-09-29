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
        
        $classIds = $this->classIds()->flip();
        $perm = !$class ? true : $classIds->has($class->id);
        [$gradeId, $educatorIds, $deptId, $ids] = array_map(
            function ($field) use ($class) {
                return Request::input($field)
                    ?? ($class ? explode(',', $class->{$field}) : null);
            }, ['grade_id', 'educator_ids', 'department_id', 'ids']
        );
        if (isset($gradeId, $educatorIds)) {
            $perm &= $this->gradeIds()->flip()->has($gradeId) &&
                $this->contactIds('educator')->flip()->has(array_values($educatorIds));
        }
        empty($deptId) ?: $perm &= $this->departmentIds()->flip()->has($deptId);
        !$ids ?: $perm &= $classIds->has(array_values($ids));
        
        return $this->action($user) && $perm;
        
    }
    
}

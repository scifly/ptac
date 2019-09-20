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
     * @throws Exception
     */
    public function operation(User $user, Squad $class = null) {
        
        $perm = true;
        [$gradeId, $educatorIds] = array_map(
            function ($field) use ($class) {
                return Request::input($field)
                    ?? ($class ? explode(',', $class->{$field}) : null);
            }, ['grade_id', 'educator_ids']
        );
        if (isset($gradeId, $educatorIds)) {
            $perm = collect($this->gradeIds())->has($gradeId) &&
                collect($this->contactIds('educator'))->has($educatorIds);
        }
        $deptId = Request::input('department_id');
        !$deptId ?: $perm &= collect($this->departmentIds())->has($deptId);
        
        return $this->action($user) && $perm;
        
    }
    
}

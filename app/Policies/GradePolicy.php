<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Grade, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;
use ReflectionException;

/**
 * Class GradePolicy
 * @package App\Policies
 */
class GradePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Grade|null $grade
     * @return bool
     * @throws ReflectionException
     * @throws Exception
     */
    public function operation(User $user, Grade $grade = null) {
    
        $perm = !$grade ? true : collect($this->gradeIds())->has($grade->id);
        [$schoolId, $educatorIds, $deptId] = array_map(
            function ($field) use ($grade) {
                return Request::input($field)
                    ?? ($grade ? explode(',', $grade->{$field}) : null);
            }, ['school_id', 'educator_ids', 'department_id']
        );
        if (isset($schoolId, $educatorIds)) {
            $perm &= collect($this->schoolIds())->has($schoolId) &&
                collect($this->contactIds('educator'))->has(array_values($educatorIds));
        }
        empty($deptId) ?: $perm &= collect($this->departmentIds())->has($deptId);
        
        return $this->action($user) && $perm;
    
    }
    
}
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
    
        $gradeIds = $this->gradeIds()->flip();
        $perm = !$grade ? true : $gradeIds->has($grade->id);
        [$schoolId, $educatorIds, $deptId, $ids] = array_map(
            function ($field) use ($grade) {
                $value = Request::input($field) ?? ($grade ? $grade->{$field} : null);
                !is_array($value) ?: $value = join(',', $value);
                return $value ? explode(',', $value) : null;
            }, ['school_id', 'educator_ids', 'department_id', 'ids']
        );
        if (isset($schoolId, $educatorIds)) {
            $perm &= $this->schoolIds()->flip()->has($schoolId) &&
                $this->contactIds('educator')->flip()->has(array_values($educatorIds));
        }
        empty($deptId) ?: $perm &= $this->departmentIds()->flip()->has($deptId);
        !$ids ?: $perm &= $gradeIds->has(array_values($ids));
        
        return $this->action($user) && $perm;
    
    }
    
}
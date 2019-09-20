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
     * 权限判断
     *
     * @param User $user
     * @param Grade|null $grade
     * @return bool
     * @throws ReflectionException
     * @throws Exception
     * @throws Exception
     */
    function operation(User $user, Grade $grade = null) {
    
        if ($grade) {
            $perm = collect($this->departmentIds())->has(Request::input('department_id'))
                && collect($this->contactIds('educator'))->has(array_values(Request::input('educator_ids')))
                && collect($this->gradeIds())->has($grade->id);
        }
        
        return $this->action($user) && ($perm ?? true);
    
    }
    
}
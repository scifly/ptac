<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Student, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class StudentPolicy
 * @package App\Policies
 */
class StudentPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Student $student
     * @return bool
     * @throws Exception
     */
    public function operation(User $user, Student $student = null) {
        
        $perm = true;
        [$userId, $classId, $ids] = array_map(
            function ($field) use ($student) {
                return $this->field($field, $student);
            }, ['user_id', 'class_id', 'ids']
        );
        !$userId ?: $perm &= collect(explode(',', $this->visibleUserIds()))->flip()->has($userId);
        !$classId ?: $perm &= $this->classIds()->flip()->has($classId);
        !$ids ?: $perm &= $this->contactIds('student')->flip()->has(array_values($ids));
        
        return $this->action($user) && $perm;
        
    }
    
}

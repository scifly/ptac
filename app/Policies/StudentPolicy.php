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
        [$userId, $classId] = array_map(
            function ($field) use ($student) {
                return $this->field($field, $student);
            }, ['user_id', 'class_id']
        );
        !$userId ?: $perm &= collect(explode(',', $this->visibleUserIds()))->flip()->has($userId);
        !$classId ?: $perm &= collect($this->classIds())->flip()->has($classId);
        
        return $this->action($user) && $perm;
        
    }
    
}

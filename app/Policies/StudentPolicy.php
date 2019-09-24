<?php
namespace App\Policies;

use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Student;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class StudentPolicy
 * @package App\Policies
 */
class StudentPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * 权限判断
     *
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
        !$userId ?: $perm &= collect(explode(',', $this->visibleUserIds()))->has($userId);
        !$classId ?: $perm &= collect($this->classIds())->has($classId);
        
        return $this->action($user) && $perm;
        
    }
    
}

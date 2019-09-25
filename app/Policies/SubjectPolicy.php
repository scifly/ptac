<?php
namespace App\Policies;

use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Grade;
use App\Models\Subject;
use App\Models\User;
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SubjectPolicy
 * @package App\Policies
 */
class SubjectPolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
    /**
     * @param User $user
     * @param Subject|null $subject
     * @return bool
     * @throws Exception
     */
    function operation(User $user, Subject $subject = null) {
        
        [$schoolId, $gradeIds] = array_map(
            function ($field) use ($subject) {
                return $this->field($field, $subject);
            }, ['school_id', 'grade_ids']
        );
        if (isset($schoolId, $gradeIds)) {
            $perm = $schoolId == $this->schoolId()
                && Grade::whereSchoolId($this->schoolId())->pluck('id')->flip()->has(
                    explode(',', $gradeIds)
                );
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}

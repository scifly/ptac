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
        
        [$schoolId, $gradeIds, $ids] = array_map(
            function ($field) use ($subject) {
                return $this->field($field, $subject);
            }, ['school_id', 'grade_ids', 'ids']
        );
        $_schoolId = $this->schoolId();
        if (isset($schoolId, $gradeIds)) {
            $perm = $schoolId == $_schoolId
                && Grade::whereSchoolId($_schoolId)->pluck('id')->flip()->has(
                    array_values(explode(',', $gradeIds))
                );
        }
        !$ids ?: $perm = Subject::whereSchoolId($_schoolId)
            ->pluck('id')->flip()->has(array_values($ids));
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}

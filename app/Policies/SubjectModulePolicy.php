<?php
namespace App\Policies;

use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Subject;
use App\Models\SubjectModule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class SubjectModulePolicy
 * @package App\Policies
 */
class SubjectModulePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param SubjectModule|null $sm
     * @return bool
     */
    public function operation(User $user, SubjectModule $sm = null) {
        
        [$subjectId, $ids] = array_map(
            function ($field) use ($sm) {
                return $this->field($field, $sm);
            }, ['subject_id', 'ids']
        );
        $schoolId = $this->schoolId();
        !$subjectId ?: $perm = Subject::find($subjectId)->school_id == $schoolId;
        !$ids ?: $perm = SubjectModule::whereIn(
            'subject_id', Subject::whereSchoolId($schoolId)
        )->pluck('id')->flip()->has(array_values($ids));
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}

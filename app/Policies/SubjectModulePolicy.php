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
        
        if ($subjectId = $this->field('subject_id', $sm)) {
            $perm = Subject::find($subjectId)->school_id == $this->schoolId();
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}

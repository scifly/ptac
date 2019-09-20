<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Score, Subject, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;
use ReflectionException;

/**
 * Class ScorePolicy
 * @package App\Policies
 */
class ScorePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Score|null $score
     * @return bool
     * @throws ReflectionException
     * @throws Exception
     */
    function operation(User $user, Score $score = null) {

        if ($score) {
            $perm = collect($this->contactIds('student'))->has($score->student_id)
                && Subject::whereSchoolId($this->schoolId())->pluck('id')->has($score->subject_id)
                && collect($this->examIds())->has($score->exam_id);
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}
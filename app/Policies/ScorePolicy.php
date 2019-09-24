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

        [$studentId, $subjectId, $examId] = array_map(
            function ($field) use ($score) {
                return $this->field($field, $score);
            }, ['student_id', 'subject_id', 'exam_id']
        );
        if (isset($studentId, $subjectId, $examId)) {
            $perm = collect($this->contactIds('student'))->has($studentId)
                && Subject::whereSchoolId($this->schoolId())->pluck('id')->has($subjectId)
                && collect($this->examIds())->has($examId);
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}
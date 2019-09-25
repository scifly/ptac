<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Exam, ExamType, Subject, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class ExamPolicy
 * @package App\Policies
 */
class ExamPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Exam|null $exam
     * @return bool
     * @throws Exception
     */
    function operation(User $user, Exam $exam = null) {

        [$subjectIds, $classIds, $examTypeId] = array_map(
            function ($field) use ($exam) {
                return explode(',', $this->field($field, $exam));
            }, ['subject_ids', 'class_ids', 'exam_type_id']
        );
        if (isset($subjectIds, $classIds, $examTypeId)) {
            $schoolId = $this->schoolId();
            $perm = Subject::whereSchoolId($schoolId)->pluck('id')->flip()->has($subjectIds)
                && collect($this->classIds())->flip()->has($classIds)
                && ExamType::whereSchoolId($schoolId)->pluck('id')->flip()->has($examTypeId)
                && (!$exam ? true : collect($this->examIds())->flip()->has($exam->id));
        }
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}
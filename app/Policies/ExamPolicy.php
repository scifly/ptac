<?php
namespace App\Policies;

use App\Helpers\{ModelTrait, PolicyTrait};
use App\Models\{Exam, User};
use Exception;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Collection;

/**
 * Class ExamPolicy
 * @package App\Policies
 */
class ExamPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * @param User $user
     * @param Exam|null $exam
     * @return bool
     * @throws Exception
     */
    function operation(User $user, Exam $exam = null) {

        [$subjectIds, $classIds, $examTypeId, $ids] = array_map(
            function ($field) use ($exam) {
                return $this->field($field, $exam);
            }, ['subject_ids', 'class_ids', 'exam_type_id', 'ids']
        );
        $schoolId = $this->schoolId();
        /** @var Collection $sIds */
        /** @var Collection $etIds */
        [$sIds, $etIds] = array_map(
            function ($name) use ($schoolId) {
                return $name::{'whereSchoolId'}($schoolId)->pluck('id')->flip();
            }, ['Subject', 'ExamType']
        );
        /** @var Collection $cIds */
        /** @var Collection $eIds */
        [$cIds, $eIds] = array_map(
            function ($name) {
                return collect($this->{$name}())->flip();
            }, ['classIds', 'examIds']
        );
        if (isset($subjectIds, $classIds, $examTypeId)) {
            [$subjectIds, $classIds, $examTypeId] = array_map(
                function ($ids) {
                    return explode(',', $ids);
                }, [$subjectIds, $classIds, $examTypeId]
            );
            $perm = $sIds->has($subjectIds) && $etIds->has($examTypeId)
                && $cIds->has($classIds) && (!$exam ? true : $eIds->has($exam->id));
        }
        !$ids ?: $perm = $eIds->has(array_values($ids));
        
        return $this->action($user) && ($perm ?? true);
        
    }
    
}
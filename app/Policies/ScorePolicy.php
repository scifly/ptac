<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Score;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

class ScorePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Score|null $score
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Score $score = null, $abort = false) {
        
        abort_if(
            $abort && !$score,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        $isScoreAllowed = $isSubjectAllowed = $isExamAllowed = $isMaxScoreAllowed = false;
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['store', 'update'])) {
            $subjectId = Request::input('subject_id');
            $examId = Request::input('exam_id');
            $grade = Request::input('score');
            $allowedSubjectIds = Subject::whereSchoolId($this->schoolId())
                ->get()->pluck('id')->toArray();
            $isSubjectAllowed = in_array($subjectId, $allowedSubjectIds);
            $isExamAllowed = in_array($examId, $this->examIds());
            $isMaxScoreAllowed = $grade <= Subject::find($subjectId)->max_score;
        }
        if (in_array($action, ['edit', 'update', 'delete'])) {
            $isScoreAllowed = in_array($score->student_id, $this->contactIds('student'));
        }
        switch ($action) {
            case 'index':
                return $isSuperRole ? true : $this->action($user);
            case 'create':
                if (Request::route('examId')) {
                    $isExamAllowed = in_array(Request::route('examId'), $this->examIds());
                    return $isSuperRole ? $isExamAllowed : ($isExamAllowed && $this->action($user));
                }
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole
                    ? ($isSubjectAllowed && $isExamAllowed && $isMaxScoreAllowed)
                    : ($isSubjectAllowed && $isExamAllowed && $isMaxScoreAllowed && $this->action($user));
            case 'edit':
                if (Request::route('examId')) {
                    $isExamAllowed = in_array(Request::route('examId'), $this->examIds());
                    return $isSuperRole ? $isExamAllowed : ($isExamAllowed && $this->action($user));
                }
                return $isSuperRole ? $isScoreAllowed : ($isScoreAllowed && $this->action($user));
            case 'delete':
                return $isSuperRole ? $isScoreAllowed : ($isScoreAllowed && $this->action($user));
            case 'update':
                return $isSuperRole
                    ? ($isScoreAllowed && $isExamAllowed && $isScoreAllowed && $isMaxScoreAllowed)
                    : ($isScoreAllowed && $isExamAllowed && $isScoreAllowed && $isMaxScoreAllowed && $this->action($user));
            case 'send':
                if (Request::has('examId')) {
                    $examId = Request::input('examId');
                    $classId = Request::input('classId');
                    $subjectIds = Request::input('subjectIds');
                    $isExamAllowed = in_array($examId, $this->examIds());
                    $isClassAllowed = $isSubjectAllowed = true;
                    if ($classId && $subjectIds) {
                        $isClassAllowed = in_array($classId, $this->classIds());
                        $allowedSubjectIds = Subject::whereSchoolId($this->schoolId())
                            ->pluck('id')->toArray();
                        $isSubjectAllowed = empty(array_diff(
                            $subjectIds, array_merge([-1], $allowedSubjectIds)
                        ));
                    }
                    
                    return $isSuperRole
                        ? ($isExamAllowed && $isClassAllowed && $isSubjectAllowed)
                        : ($isExamAllowed && $isClassAllowed && $isSubjectAllowed && $this->action($user));
                }
                return $isSuperRole ? true : $this->action($user);
            case 'import':
            case 'export':
                if (Request::route('examId')) {
                    $isExamAllowed = in_array(Request::route('examId'), $this->examIds());
                    return $isSuperRole ? $isExamAllowed : ($isExamAllowed && $this->action($user));
                }
                return $isSuperRole ? true : $this->action($user);
            case 'rank':
                $examId = Request::route('examId');
                $isExamAllowed = in_array($examId, $this->examIds());
                $examScore = Score::whereExamId($examId)->first();
                abort_if(!$examScore, HttpStatusCode::NOT_FOUND, '此次考试的成绩尚未录入');
                return $isSuperRole ? $isExamAllowed : ($isExamAllowed && $this->action($user));
            case 'stat':
                if (Request::method() == 'POST') {
                    $examId = Request::input('examId');
                    $isExamAllowed = in_array($examId, $this->examIds());
                    return $isSuperRole ? $isExamAllowed : ($isExamAllowed && $this->action($user));
                }
                if (Request::route('type') && Request::route('value')) {
                    $type = Request::route('type');
                    $value = Request::route('value');
                    $isTypeAllowed = in_array($type, ['class', 'student']);
                    if ($isTypeAllowed) {
                        if ($type == 'class') {
                            $isExamAllowed = in_array($value, $this->examIds());
                            return $isSuperRole ? $isExamAllowed : ($isExamAllowed && $this->action($user));
                        } else {
                            $isClassAllowed = in_array($value, $this->classIds());
                            return $isSuperRole ? $isClassAllowed : ($isClassAllowed && $this->action($user));
                        }
                    } else {
                        return false;
                    }
                }
                return $isSuperRole ? true : $this->action($user);
            default:
                return false;
        }
        
    }
    
}
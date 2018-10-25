<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Menu;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class ExamPolicy
 * @package App\Policies
 */
class ExamPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    protected $menu;
    
    /**
     * Create a new policy instance.
     *
     * @param Menu $menu
     */
    public function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Exam|null $exam
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Exam $exam = null, $abort = false) {

        abort_if(
            $abort && !$exam,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $action = explode('/', Request::path())[1];
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $isExamAllowed = $isExamTypeAllowed = $isClassAllowed = $isSubjectAllowed = false;
        if (in_array($action, ['store', 'update'])) {
            $examTypeId = Request::input('exam_type_id');
            $classIds = Request::input('class_ids');
            $subjectIds = Request::input('subject_ids');
            $allowedExamTypeIds = ExamType::whereIn('school_id', $this->schoolIds())->pluck('id')->toArray();
            $isExamTypeAllowed = in_array($examTypeId, $allowedExamTypeIds);
            $allowedSubjectIds = Subject::whereIn('school_id', $this->schoolIds())->pluck('id')->toArray();
            $isSubjectAllowed = empty(array_diff($subjectIds, $allowedSubjectIds));
            $isClassAllowed = empty(array_diff($classIds, $this->classIds()));
        }
        if (in_array($action, ['show', 'edit', 'update', 'delete'])) {
            $isExamAllowed = empty(array_diff(explode(',', $exam->class_ids), $this->classIds()));
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole
                    ? ($isExamTypeAllowed && $isClassAllowed && $isSubjectAllowed)
                    : ($isExamTypeAllowed && $isClassAllowed && $isSubjectAllowed && $this->action($user));
            case 'show':
            case 'edit':
            case 'delete':
                return $isSuperRole ? $isExamAllowed : ($isExamAllowed && $this->action($user));
            case 'update':
                return $isSuperRole
                    ? ($isExamAllowed && $isExamTypeAllowed && $isClassAllowed && $isSubjectAllowed)
                    : ($isExamAllowed && $isExamTypeAllowed && $isClassAllowed && $isSubjectAllowed && $this->action($user));
            default:
                return false;
        }
        
    }
    
}
<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Corp;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Menu;
use App\Models\School;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

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
    
    public function create(User $user) {
    
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
    
        return $this->action($user);
        
    }
    
    public function store(User $user) {
    
        return $this->permit($user);
        
    }
    
    public function show(User $user, Exam $exam) {
        
        return $this->objectPerm($user, $exam);
        
    }
    
    public function edit(User $user, Exam $exam) {
        
        return $this->objectPerm($user, $exam);
        
    }
    
    public function update(User $user, Exam $exam) {
        
        abort_if(
            !$exam,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        
        return $this->permit($user);
        
    }
    
    public function destroy(User $user, Exam $exam) {
        
        return $this->objectPerm($user, $exam);
        
    }
    
    /**
     * (e)dit, (u)pdate, (d)estroy
     *
     * @param User $user
     * @param Exam $exam
     * @return bool
     */
    private function objectPerm(User $user, Exam $exam) {
        
        $role = $user->group->name;
        $rootMenuId = $this->menu->rootMenuId();
        switch ($role) {
            case '运营':
                return true;
            case '企业':
                # $userCorp - the Corp to which the user belongs
                $userCorp = Corp::whereMenuId($rootMenuId)->first();
                return $exam->examType->school->corp_id == $userCorp->id;
            case '学校':
                # $userSchool - the School to which the user belongs
                $userSchool = School::whereMenuId($rootMenuId)->first();
                return $exam->examType->school_id == $userSchool->id;
            default:
                return ($user->educator->school_id == $exam->examType->school_id)
                    && (empty(array_diff(explode(',', $exam->class_ids), $this->classIds())) ? true : false)
                    && $this->action($user);
        }
        
    }
    
    private function permit($user) {
    
        switch ($user->group->name) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return isAllowed();
            default:
                return isAllowed() && $this->action($user);
        }
    
        function isAllowed() {
        
            $examTypeId = Request::input('exam_type_id');
            $classIds = Request::input('class_ids');
            $subjectIds = Request::input('subject_ids');
            $allowedExamTypeIds = ExamType::whereEnabled(1)
                ->whereIn('school_id', $this->schoolIds())
                ->get()->pluck('id')->toArray();
            $allowedSubjectIds = Subject::whereEnabled(1)
                ->whereIn('school_id', $this->schoolIds())
                ->get()->pluck('id')->toArray();
        
            return in_array($examTypeId, $allowedExamTypeIds)
                && empty(array_diff($classIds, $this->classIds()))
                && empty(array_diff($subjectIds, $allowedSubjectIds));
        
        }
    }
    
}

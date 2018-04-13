<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\ActionGroup;
use App\Models\Corp;
use App\Models\Exam;
use App\Models\Menu;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
        
        return $this->classPerm($user);
        
    }
    
    public function store(User $user) {
    
        return $this->classPerm($user);
        
    }
    
    /**
     * (c)reate, (s)tore
     *
     * @param User $user
     * @return bool
     */
    private function classPerm(User $user) {
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        
        return $this->action($user);
        
    }
    
    public function show(User $user, Exam $exam) {
        
        return $this->objectPerm($user, $exam);
        
    }

    public function edit(User $user, Exam $exam) {
        
        return $this->objectPerm($user, $exam);
        
    }

    public function update(User $user, Exam $exam) {
        
        return $this->objectPerm($user, $exam);
        
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
    
}

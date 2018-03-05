<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Models\ActionGroup;
use App\Models\Corp;
use App\Models\Exam;
use App\Models\School;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExamPolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * (c)reate, (s)tore
     *
     * @param User $user
     * @return bool
     */
    public function cs(User $user) {
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        
        return ActionGroup::whereGroupId($user->group_id)->first() ? true : false;
        
    }
    
    /**
     * (e)dit, (u)pdate, (d)estroy
     *
     * @param User $user
     * @param Exam $exam
     * @return bool
     */
    public function seud(User $user, Exam $exam) {
        
        $role = $user->group->name;
        switch ($role) {
            case '运营': return true;
            case '企业':
                # $userCorp - the Corp to which the user belongs
                $userCorp = Corp::whereDepartmentId($user->topDeptId())->first();
                return $exam->examType->school->corp_id == $userCorp->id;
            case '学校':
                # $userSchool - the School to which the user belongs
                $userSchool = School::whereDepartmentId($user->topDeptId())->first();
                return $exam->examType->school_id == $userSchool->id;
            default:
                $class = new Squad();
                $allowedClassIds = $class->classIds();
                unset($class);
                return ($user->educator->school_id == $exam->examType->school_id)
                    && (empty(array_diff(explode(',', $exam->class_ids), $allowedClassIds)) ? true : false)
                    && (ActionGroup::whereGroupId($user->group_id)->first() ? true : false);
        }
        
    }
    
}

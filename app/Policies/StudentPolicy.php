<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\ActionGroup;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * (c)reate, (s)tore, (i)mport, (e)xport
     *
     * @param User $user
     * @return bool
     */
    public function csie(User $user) {
    
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        
        return $this->action($user);
    
    }
    
    /**
     * (s)how, (e)dit, (u)pdate, (d)estroy
     *
     * @param User $user
     * @param Student $student
     * @return bool
     */
    public function seud(User $user, Student $student) {
        
        abort_if(
            !$student,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return in_array($student->id, $this->contactIds('student'));
            default:
                return in_array($student->id, $this->contactIds('student')) && $this->action($user);
        }
        
    }
    
}

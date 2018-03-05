<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\ActionGroup;
use App\Models\Corp;
use App\Models\Grade;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GradePolicy {
    
    use HandlesAuthorization, ModelTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function cs(User $user) {
        
        $role = $user->group->name;
        switch ($role) {
            case '运营': return true;
            case '企业':
                $corp = Corp::whereDepartmentId($user->topDeptId())->first();
                return in_array(
                    $this->schoolId(),
                    $corp->schools->pluck('id')->toArray()
                );
            case '学校':
                $school = School::whereDepartmentId($user->topDeptId())->first();
                return $this->schoolId() == $school->id;
            default:
                return ($user->educator->school_id == $this->schoolId())
                    && (ActionGroup::whereGroupId($user->group_id)->first() ? true : false);
        }
        
        
    }
    
    public function eud(User $user, Grade $grade) {
    
        abort_if(
            !$grade,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        switch ($role) {
            case '运营': return true;
            case '企业':
                $corp = Corp::whereDepartmentId($user->topDeptId())->first();
                return in_array(
                    $grade->school_id,
                    $corp->schools->pluck('id')->toArray()
                );
            default:
                return ($user->educator->school_id == $grade->school_id)
                    && (ActionGroup::whereGroupId($user->group->id)->first() ? true : false)
                    && (in_array($grade->id, $grade->gradeIds()));
        }
    
    }
    
}

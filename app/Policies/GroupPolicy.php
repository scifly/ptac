<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Models\Corp;
use App\Models\Group;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupPolicy {
    
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    public function cs(User $user) {
    
        return in_array(
            $user->group->name,
            Constant::SUPER_ROLES
        );
    
    }
    
    public function eud(User $user, Group $group) {
        
        abort_if(
            !$group,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        switch ($role) {
            case '运营': return true;
            case '企业':
                $corp = Corp::whereDepartmentId($user->topDeptId())->first();
                return in_array(
                    $group->school->id, $corp->schools->pluck('id')->toArray()
                );
            case '学校':
                $school = School::whereDepartmentId($user->topDeptId())->first();
                return $school->id == $group->school->id;
            default: return false;
        }
        
    }
    
}

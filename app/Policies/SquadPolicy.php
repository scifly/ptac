<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\ActionGroup;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SquadPolicy {
    
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
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        
        return ActionGroup::whereGroupId($user->group_id) ? true : false;
        
    }
    
    public function eud(User $user, Squad $class) {
        
        abort_if(
            !$class,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return in_array($class->grade->school_id, $this->schoolIds());
            default:
                return in_array($class->grade_id, $this->gradeIds())
                    && (ActionGroup::whereGroupId($user->group_id)->first() ? true : false);
        }
        
    }
    
}

<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\ActionGroup;
use App\Models\SubjectModule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectModulePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
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
        
        return $this->action($user);
        
    }
    
    public function eud(User $user, SubjectModule $sm) {
        
        abort_if(
            !$sm,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return in_array($sm->subject->school_id, $this->schoolIds());
            default:
                return in_array($sm->subject->school_id, $this->schoolIds()) && $this->action($user);
        }
        
    }
    
}

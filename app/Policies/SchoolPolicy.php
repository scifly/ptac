<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy {
    
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
        
        return in_array($user->group->name, ['运营', '企业']);
        
    }
    
    /**
     * (s)how, (e)dit, (u)pdate
     *
     * @param User $user
     * @param School $school
     * @return bool
     */
    public function seu(User $user, School $school) {
        
        abort_if(
            !$school,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if (!in_array($user->group->name, Constant::SUPER_ROLES)) {
            return false;
        }
        
        return in_array($this->schoolId(), $this->schoolIds());
        
    }
    
}

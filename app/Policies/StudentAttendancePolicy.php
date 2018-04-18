<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\PolicyTrait;
use App\Models\StudentAttendance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentAttendancePolicy {
    
    use HandlesAuthorization, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * 
     *
     * @param User $user
     * @param StudentAttendance|null $sa
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, StudentAttendance $sa = null, $abort = false) {
        
        abort_if(
            $abort && !$sa,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        
        if (in_array($user->group->name, Constant::SUPER_ROLES)) {
            return true;
        }
        
        return $this->action($user);
        
    }
    
}

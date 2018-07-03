<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\StudentAttendance;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class StudentAttendancePolicy
 * @package App\Policies
 */
class StudentAttendancePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() {
        //
    }
    
    /**
     * 权限判断
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
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
            case 'export':
                return $isSuperRole ? true : $this->action($user);
            case 'stat':
            case 'detail':
                $classId = Request::input('class_id');
                $isClassAllowed = in_array($classId, $this->classIds());
                return $isSuperRole ? $isClassAllowed : ($isClassAllowed && $this->action($user));
            default:
                return false;
        }
        
    }
    
}

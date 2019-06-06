<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class StudentPolicy
 * @package App\Policies
 */
class StudentPolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param Student $student
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, Student $student = null, $abort = false) {
        
        abort_if(
            $abort && !$student,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
            case 'create':
            case 'store':
            case 'import':
            case 'export':
            case 'issue':
            case 'grant':
                return $isSuperRole ? true : $this->action($user);
            case 'show':
            case 'edit':
            case 'update':
            case 'delete':
                $schoolId = $student->squad->grade->school_id;
                return $isSuperRole
                    ? (in_array($schoolId, $this->schoolIds()))
                    : (in_array($schoolId, $this->schoolIds()) && $this->action($user));
            default:
                return false;
        }
        
    }
    
}

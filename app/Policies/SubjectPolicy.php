<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class SubjectPolicy
 * @package App\Policies
 */
class SubjectPolicy {
    
    use HandlesAuthorization, PolicyTrait, ModelTrait;
    
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
     * @param Subject|null $subject
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, Subject $subject = null, $abort = false) {
        
        abort_if(
            $abort && !$subject,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        $isSubjectAllowed = $isGradeAllowed = false;
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['store', 'update'])) {
            $gradeIds = Request::input('grade_ids');
            $isGradeAllowed = empty(array_diff($gradeIds, $this->gradeIds()));
        }
        if (in_array($action, ['edit', 'update', 'delete'])) {
            $isSubjectAllowed = $subject->school_id == $this->schoolId();
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole ? $isGradeAllowed : ($isGradeAllowed && $this->action($user));
            case 'edit':
            case 'delete':
                return $isSuperRole ? $isSubjectAllowed : ($isSubjectAllowed && $this->action($user));
            case 'update':
                return $isSuperRole
                    ? ($isSubjectAllowed && $isGradeAllowed)
                    : ($isSubjectAllowed && $isGradeAllowed && $this->action($user));
            default:
                return false;
        }
        
    }
    
}

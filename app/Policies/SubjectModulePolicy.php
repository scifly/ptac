<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\School;
use App\Models\SubjectModule;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class SubjectModulePolicy
 * @package App\Policies
 */
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
    
    /**
     * 权限判断
     *
     * @param User $user
     * @param SubjectModule|null $sm
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, SubjectModule $sm = null, $abort = false) {
        
        abort_if(
            $abort && !$sm,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $isSmAllowed = $isSubjectAllowed = false;
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['store', 'update'])) {
            $subjectId = Request::input('subject_id');
            $allowedSubjectIds = School::find($this->schoolId())
                ->subjects->pluck('id')->toArray();
            $isSubjectAllowed = in_array($subjectId, $allowedSubjectIds);
        }
        if (in_array($action, ['edit', 'update', 'delete'])) {
            $isSmAllowed = $sm->subject->school_id == $this->schoolId();
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole ? $isSubjectAllowed : ($isSubjectAllowed && $this->action($user));
            case 'edit':
            case 'delete':
                return $isSuperRole ? $isSmAllowed : ($isSmAllowed && $this->action($user));
            case 'update':
                return $isSuperRole
                    ? ($isSmAllowed && $isSubjectAllowed)
                    : ($isSmAllowed && $isSubjectAllowed && $this->action($user));
            default:
                return false;
            
        }
        
    }
    
}

<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\ProcedureStep;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class ProcedureStepPolicy
 * @package App\Policies
 */
class ProcedureStepPolicy {
    
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
     * @param ProcedureStep $ps
     * @param bool $abort
     * @return bool
     */
    function opertion(User $user, ProcedureStep $ps, $abort = false) {
        
        abort_if(
            $abort && !$ps,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $isPsAllowed = $isApproverUserAllowed = $isRelatedUserAllowed = false;
        $isSuperUser = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['store', 'update'])) {
            $approverUserIds = explode(',', Request::input('approver_user_ids'));
            $relatedUserIds = explode(',', Request::input('related_user_ids'));
            $isApproverUserAllowed = empty(array_diff($approverUserIds, $this->contactIds('educator')));
            $isRelatedUserAllowed = empty(array_diff($relatedUserIds, $this->contactIds('educator')));
        }
        if (in_array($action, ['edit', 'update', 'delete'])) {
            $isPsAllowed = in_array($ps->procedure->school_id, $this->schoolIds());
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperUser ? true : $this->action($user);
            case 'store':
                return $isSuperUser
                    ? ($isApproverUserAllowed && $isRelatedUserAllowed)
                    : ($isApproverUserAllowed && $isRelatedUserAllowed && $this->action($user));
            case 'edit':
            case 'delete':
                return $isSuperUser ? $isPsAllowed : ($isPsAllowed && $this->action($user));
            case 'update':
                return $isSuperUser
                    ? ($isPsAllowed && $isApproverUserAllowed && $isRelatedUserAllowed)
                    : ($isPsAllowed && $isApproverUserAllowed && $isRelatedUserAllowed && $this->action($user));
            default:
                return false;
                
        }
        
    }
    
}

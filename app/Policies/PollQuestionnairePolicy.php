<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\PollQuestionnaire;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class PollQuestionnairePolicy
 * @package App\Policies
 */
class PollQuestionnairePolicy {
    
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
     * @param PollQuestionnaire|null $pq
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, PollQuestionnaire $pq = null, $abort = false) {
        
        abort_if(
            $abort && !$pq,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $isSchoolAllowed = false;
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['show', 'edit', 'update', 'delete'])) {
            $allowedSchoolIds = $this->schoolIds();
            $isSchoolAllowed = in_array($pq->school_id, $allowedSchoolIds);
        }
        switch ($action) {
            case 'index':
            case 'create':
            case 'store':
                return $isSuperRole ? true : $this->action($user);
            case 'show':
            case 'edit':
            case 'update':
            case 'delete':
                return $isSuperRole
                    ? $isSchoolAllowed
                    : $this->action($user) && $isSchoolAllowed && ($pq->user_id == $user->id);
            default:
                return false;
        }
        
    }
    
}

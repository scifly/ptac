<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\PollQuestionnaire;
use App\Models\PollQuestionnaireSubject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class PollQuestionnaireSubjectPolicy
 * @package App\Policies
 */
class PollQuestionnaireSubjectPolicy {
    
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
     * @param User $user
     * @return bool
     */
    public function cs(User $user) {
        
        switch ($user->role()) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return in_array($this->schoolId(), $this->schoolIds());
            default:
                return in_array($this->schoolId(), $this->schoolIds()) && $this->action($user);
        }
        
    }
    
    /**
     * @param User $user
     * @param PollQuestionnaireSubject|null $pqs
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, PollQuestionnaireSubject $pqs = null, $abort = false) {
        
        abort_if(
            $abort && !$pqs,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->role();
        if ($role == '运营') { return true; }
        $isPqsAllowed = $isPqAllowed = false;
        $isSuperRole = in_array($role, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['store', 'update'])) {
            $pqId = Request::input('pq_id');
            $allowedPqIds = PollQuestionnaire::whereSchoolId($this->schoolId())->pluck('id')->toArray();
            $isPqAllowed = in_array($pqId, $allowedPqIds);
        }
        if (in_array($action, ['show', 'edit', 'update', 'delete'])) {
            $isPqsAllowed = $pqs->pollQuestionnaire->school_id == $this->schoolId();
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole ? $isPqAllowed : ($isPqsAllowed && $this->action($user));
            case 'show':
            case 'edit':
            case 'delete':
                return $isSuperRole
                    ? $isPqsAllowed
                    : $this->action($user) && $isPqsAllowed && $pqs->pollQuestionnaire->user_id == $user->id;
            case 'update':
                return $isSuperRole
                    ? $isPqsAllowed && $isPqAllowed
                    : $this->action($user) && $isPqsAllowed && $isPqAllowed && $pqs->pollQuestionnaire->user_id == $user->id;
            default:
                return false;
        }
        
    }
    
}

<?php
namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\PollQuestionnaire;
use App\Models\PollQuestionnaireSubject;
use App\Models\PollQuestionnaireSubjectChoice;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class PollQuestionnaireSubjectChoicePolicy
 * @package App\Policies
 */
class PollQuestionnaireSubjectChoicePolicy {
    
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
     * @param PollQuestionnaireSubjectChoice|null $pqsc
     * @param bool $abort
     * @return bool
     */
    public function operation(User $user, PollQuestionnaireSubjectChoice $pqsc = null, $abort = false) {
        
        abort_if(
            $abort && !$pqsc,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        if ($user->group->name == '运营') { return true; }
        $isPqscAllowed = $isPqsAllowed = false;
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        $action = explode('/', Request::path())[1];
        if (in_array($action, ['store', 'update'])) {
            $pqsId = Request::input('pqs_id');
            $allowedPqs = PollQuestionnaire::whereSchoolId($this->schoolId())
                ->get()->pluck('id')->toArray();
            $allowedPqsIds = PollQuestionnaireSubject::whereIn('pqs_id', $allowedPqs)
                ->get()->pluck('id')->toArray();
            $isPqsAllowed = in_array($pqsId, $allowedPqsIds);
        }
        if (in_array($action, ['show', 'edit', 'update', 'delete'])) {
            $isPqscAllowed = in_array(
                $pqsc->pollQuestionnaireSubject->pollQuestionnaire->school_id,
                $this->schoolIds()
            );
        }
        switch ($action) {
            case 'index':
            case 'create':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                return $isSuperRole ? $isPqsAllowed : ($isPqsAllowed && $this->action($user));
            case 'show':
            case 'edit':
            case 'delete':
                return $isSuperRole ? $isPqscAllowed : ($isPqscAllowed && $this->action($user));
            case 'update':
                return $isSuperRole
                    ? ($isPqscAllowed && $isPqsAllowed)
                    : ($isPqsAllowed && $isPqscAllowed && $this->action($user));
            default:
                return false;
                
        }
        
    }
    
}

<?php
namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\ActionGroup;
use App\Models\PollQuestionnaire;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

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
    
    public function cs(User $user) {
        
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return in_array($this->schoolId(), $this->schoolIds());
            default:
                return in_array($this->schoolId(), $this->schoolIds()) && $this->action($user);
        }
        
    }
    
    public function eud(User $user, PollQuestionnaire $pq) {
        
        abort_if(
            !$pq,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        switch ($role) {
            case '运营':
                return true;
            case '企业':
            case '学校':
                return in_array($this->schoolId(), $this->schoolIds());
            default:
                return in_array($this->schoolId(), $this->schoolIds())
                    && $this->action($user)
                    && ($user->id == $pq->user_id);
        }
        
    }
    
}

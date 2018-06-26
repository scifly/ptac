<?php

namespace App\Policies;

use App\Helpers\Constant;
use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\ConferenceParticipant;
use App\Models\ConferenceQueue;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class ConferenceParticipantPolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;

    const SUPER_ROLES = ['运营', '企业', '学校'];

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
     * @param ConferenceParticipant|null $cp
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, ConferenceParticipant $cp = null, $abort = false) {
    
        abort_if(
            $abort && !$cp,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $action = explode('/', Request::path())[1];
        $isSuperRole = in_array($user->group->name, Constant::SUPER_ROLES);
        switch ($action) {
            case 'index':
                return $isSuperRole ? true : $this->action($user);
            case 'store':
                if ($isSuperRole) { return false; }
                $educatorId = Request::input('educator_id');
                $conferenceQueueId = Request::input('conference_queue_id');
                $educatorIds = explode(',', ConferenceQueue::find($conferenceQueueId)->educator_ids);
                return in_array($educatorId, $educatorIds);
            case 'show':
                return $isSuperRole
                    ? $cp->conferenceQueue->conferenceRoom->school_id == $this->schoolId()
                    : ConferenceQueue::find($cp->conference_queue_id)->user_id == $user->id;
            default:
                return false;
                
        }
        
    }
    
}

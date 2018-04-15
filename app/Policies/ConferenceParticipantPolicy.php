<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Models\ConferenceParticipant;
use App\Models\ConferenceQueue;
use App\Models\ConferenceRoom;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

class ConferenceParticipantPolicy {

    use HandlesAuthorization, ModelTrait;

    const SUPER_ROLES = ['运营', '企业', '学校'];

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct() { }

    /**
     * Determine whether users can participate the designated conference
     *
     * @param User $user
     * @return bool
     */
    public function store(User $user) {

        // 超级管理员不得参加任何会议
        if (in_array($user->group->name, self::SUPER_ROLES)) {
            return false;
        }
        $educatorId = Request::input('educator_id');
        $conferenceQueueId = Request::input('conference_queue_id');
        $educatorIds = explode(',', ConferenceQueue::find($conferenceQueueId)->educator_ids);

        return in_array($educatorId, $educatorIds);

    }

    /**
     * Determine whether the current user can view the detail of the conference participant
     *
     * @param User $user
     * @param ConferenceParticipant $cp
     * @return bool
     */
    public function show(User $user, ConferenceParticipant $cp) {

        abort_if(
            !$cp,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        $role = $user->group->name;
        if (in_array($role, self::SUPER_ROLES)) {
            switch ($role) {
                case '运营': return true;
                case '企业':
                case '学校':
                    $conferenceRoomId = ConferenceQueue::find($cp->conference_queue_id)->conference_room_id;
                    $schoolId = ConferenceRoom::find($conferenceRoomId)->school->id;
                    return $schoolId == $this->schoolId();
                default: return false;
            }
        }

        return ConferenceQueue::find($cp->conference_queue_id)->user_id == $user->id;

    }

}

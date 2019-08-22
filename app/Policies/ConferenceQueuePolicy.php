<?php
namespace App\Policies;

use App\Helpers\{HttpStatusCode, ModelTrait, PolicyTrait};
use App\Models\{ConferenceQueue, ConferenceRoom, Educator, User};
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class ConferenceQueuePolicy
 * @package App\Policies
 */
class ConferenceQueuePolicy {
    
    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    /** Create a new policy instance. */
    public function __construct() { }
    
    /**
     * Determine if the user can (e)dit / (u)pdate / (d)elete the conference_queue
     *
     * @param User $user
     * @param ConferenceQueue $cq
     * @param bool $abort
     * @return bool
     */
    function operation(User $user, ConferenceQueue $cq = null, $abort = false) {
        
        abort_if(
            $abort && !$cq,
            HttpStatusCode::NOT_FOUND,
            __('messages.not_found')
        );
        [$crId, $eIds] = [
            $cq ? $cq->conference_room_id : Request::input('conference_room_id'),
            explode(',', $cq ? $cq->educator_ids : Request::input('educator_ids')),
        ];
        if (!isset($crId, $eIds) || $user->role() == '运营') return true;
        # 会议室所属学校id
        $schoolId = ConferenceRoom::find($crId)->school_id;
        # 发起者所属学校id
        $schoolIds = $this->schoolIds();
        if (!in_array($schoolId, $schoolIds)) return false;
        if (in_array($user->role(), ['企业', '学校'])) {
            foreach ($eIds as $eId) {
                if (Educator::find($eId)->school_id != $schoolId) return false;
            }
        } else {
            $userId = $cq ? $cq->user_id : null;
            # 发起者只能编辑/更新/删除自己发起的会议
            if ((isset($userId) && $user->id != $userId)) return false;
            # 会议发起者所属的所有部门id
            $deptIds = $user->departmentIds($user->id);
            foreach ($eIds as $eId) {
                # 发起者和与会者所属的共同部门
                $commonDeptIds = array_intersect(
                    $deptIds, $user->departmentIds(Educator::find($eId)->user_id)
                );
                if (empty($commonDeptIds)) return false;
            }
        }
        
        return true;
        
    }
    
}

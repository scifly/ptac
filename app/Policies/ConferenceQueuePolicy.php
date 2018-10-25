<?php

namespace App\Policies;

use App\Helpers\HttpStatusCode;
use App\Helpers\ModelTrait;
use App\Helpers\PolicyTrait;
use App\Models\ConferenceQueue;
use App\Models\ConferenceRoom;
use App\Models\Corp;
use App\Models\Educator;
use App\Models\Menu;
use App\Models\School;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Support\Facades\Request;

/**
 * Class ConferenceQueuePolicy
 * @package App\Policies
 */
class ConferenceQueuePolicy {

    use HandlesAuthorization, ModelTrait, PolicyTrait;
    
    protected $menu;

    /**
     * Create a new policy instance.
     *
     * @param Menu $menu
     */
    public function __construct(Menu $menu) {
        
        $this->menu = $menu;
        
    }

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
        switch ($user->role()) {
            case '运营':
                return true;
            case '企业':
                return $this->corpPerm($user, $cq);
            case '学校':
                return $this->schoolPerm($user, $cq);
            default:
                return $this->defaultPerm($user, $cq);
        }

    }

    /**
     * 判断企业级管理员权限
     *
     * @param User $user
     * @param ConferenceQueue|null $cq
     * @return bool
     */
    private function corpPerm(User $user, ConferenceQueue $cq = null) {

        list($conferenceRoomId, $educatorIds) = $this->getVars($cq);
        # 判断是否有创建会议的权限
        if (!isset($conferenceRoomId, $educatorIds)) { return true; }
    
        # 会议室所属企业
        $crCorpId = ConferenceRoom::find($conferenceRoomId)->school->corp_id;
        # 发起者所属企业
        $departmentId = $this->head($user);
        $corpId = Corp::whereDepartmentId($departmentId)->first()->id;
        # 发起者只能选取所属企业的会议室
        if ($corpId != $crCorpId) { return false; }
        # 发起者只能选取所属企业的教职员工参加会议
        foreach ($educatorIds as $id) {
            if (Educator::find($id)->school->corp_id != $corpId) {
                return false;
            }
        }

        return true;

    }

    /**
     * 判断校级管理员权限
     *
     * @param User $user
     * @param ConferenceQueue|null $cq
     * @return bool
     */
    private function schoolPerm(User $user, ConferenceQueue $cq = null) {

        list($conferenceRoomId, $educatorIds) = $this->getVars($cq);
        # 判断是否有创建会议的权限
        if (!isset($conferenceRoomId, $educatorIds)) { return true; }

        # 会议室所属学校
        $crSchoolId = ConferenceRoom::find($conferenceRoomId)->school_id;
        # 发起者所属学校
        $departmentId = $this->head($user);
        $schoolId = School::whereDepartmentId($departmentId)->first()->id;
        # 发起者只能选取所属学校的会议室
        if ($schoolId != $crSchoolId) { return false; }
        # 发起者只能选取所属学校的教职员工参加会议
        foreach ($educatorIds as $id) {
            if (Educator::find($id)->school_id != $schoolId) {
                return false;
            }
        }

        return true;

    }

    /**
     * 判断校级以下角色权限
     *
     * @param User $user
     * @param ConferenceQueue|null $cq
     * @return bool
     */
    private function defaultPerm(User $user, ConferenceQueue $cq = null) {

        list($conferenceRoomId, $educatorIds) = $this->getVars($cq);
        # 判断是否有创建会议的权限
        if (!isset($conferenceRoomId, $educatorIds)) { return $this->action($user); }
        $userId = $cq ? $cq->user_id : null;

        # 会议室所属学校
        $crSchoolId = ConferenceRoom::find($conferenceRoomId)->school_id;
        # 发起者所属学校
        $schoolId = $user->educator->school_id;
        if ($schoolId != $crSchoolId) { return false; }
        # 发起者只能编辑/更新/删除自己发起的会议
        if (isset($userId) && $user->id != $userId) { return false; }
        # 会议发起者所属的所有部门id
        $departmentIds = $user->departmentIds($user->id);
        foreach ($educatorIds as $id) {
            # 与会者所属的所有部门id
            $participantDepartmentIds = $user->departmentIds(
                Educator::find($id)->user->id
            );
            # 发起者和与会者共同属于的部门id
            $commonDepartmentIds = array_intersect($departmentIds, $participantDepartmentIds);
            # 如果发起者与与会者不属于同一部门
            if (empty($commonDepartmentIds)) { return false; }
        }

        return true;

    }

    /**
     * 获取会议室id及教职员工ids
     *
     * @param ConferenceQueue $cq
     * @return array
     */
    private function getVars(ConferenceQueue $cq): array {

        $conferenceRoomId = $cq
            ? $cq->conference_room_id
            : Request::input('conference_room_id');
        $educatorIds = $cq
            ? explode(',', $cq->educator_ids)
            : explode(',', Request::input('educator_ids'));

        return [$conferenceRoomId, $educatorIds];

    }

}

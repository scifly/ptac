<?php
namespace App\Http\ViewComposers;

use App\Models\ConferenceRoom;
use App\Models\Educator;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ConferenceQueueComposer {

    protected $conferenceRoom, $educator;

    public function __construct(ConferenceRoom $conferenceRoom, Educator $educator) {

        $this->conferenceRoom = $conferenceRoom;
        $this->educator = $educator;

    }

    public function compose(View $view) {
    
        $user = Auth::user();
        $schoolId = $user->group->school_id;
        # 仅校级角色才能创建编辑会议, 暂不考虑运营及企业级角色
        if (!isset($schoolId)) {
            $schoolId = School::whereDepartmentId($user->topDeptId($user))->first()->id;
        }
        $view->with([
            'conferenceRooms' => $this->conferenceRoom->where('school_id', $schoolId)->pluck('name', 'id'),
            'educators' => $this->educator->where('school_id', $schoolId)->pluck('name', 'id')
        ]);

    }

}
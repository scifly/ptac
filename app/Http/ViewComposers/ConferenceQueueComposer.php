<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\ConferenceRoom;
use App\Models\Educator;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ConferenceQueueComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $user = Auth::user();
        $schoolId = $user->group->school_id;
        # 仅校级角色才能创建编辑会议, 暂不考虑运营及企业级角色
        if (!isset($schoolId)) {
            $schoolId = School::whereDepartmentId($user->topDeptId())->first()->id;
        }
        $view->with([
            'conferenceRooms' => ConferenceRoom::whereSchoolId($schoolId)->pluck('name', 'id'),
            'educators' => Educator::whereSchoolId($schoolId)->pluck('name', 'id'),
            'uris' => $this->uris()
        ]);

    }

}
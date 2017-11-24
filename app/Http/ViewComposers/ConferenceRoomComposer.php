<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ConferenceRoomComposer {

    protected $school;

    public function __construct(School $school) {

        $this->school = $school;

    }

    public function compose(View $view) {

        $user = Auth::user();
        $schoolId = $user->group->school_id;
        if (!isset($schoolId)) {
            $schoolId = School::whereDepartmentId($user->topDeptId($user))->first()->id;
        }
        $view->with(['schoolId' => $schoolId]);

    }

}
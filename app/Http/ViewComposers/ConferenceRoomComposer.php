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
        $schoolId = $this->school->getSchoolId();
        $view->with(['schoolId' => $schoolId]);
    }

}
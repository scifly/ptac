<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class EducatorAttendanceSettingComposer {
    use ControllerTrait;
    protected $school;

    public function __construct(School $school) {

        $this->school = $school;

    }

    public function compose(View $view) {

        $view->with([
            'schoolId' => $this->school->getSchoolId(),
            'uris' => $this->uris()

        ]);

    }

}
<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Grade;
use App\Models\School;
use App\Models\Semester;
use Illuminate\Contracts\View\View;

class StudentAttendanceSettingIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);

    }

}
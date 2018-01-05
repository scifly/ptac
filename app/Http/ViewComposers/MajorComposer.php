<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class MajorComposer {

    use ModelTrait;

    public function compose(View $view) {

        $schoolId = School::schoolId();

        $view->with([
            'schoolId' => $schoolId,
            'subjects' => Subject::whereSchoolId($schoolId)->pluck('name', 'id'),
            'uris' => $this->uris()
        ]);

    }

}
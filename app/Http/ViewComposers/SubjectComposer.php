<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Major;
use Illuminate\Contracts\View\View;

class SubjectComposer {

    use ModelTrait;

    public function compose(View $view) {

        $schoolId = $this->schoolId();

        $view->with([
            'grades' => Grade::whereSchoolId($schoolId)->pluck('name', 'id'),
            'majors' => Major::whereSchoolId($schoolId)->pluck('name', 'id'),
            'uris' => $this->uris()
        ]);

    }

}
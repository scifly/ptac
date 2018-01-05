<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class SubjectModuleComposer {

    use ModelTrait;

    public function compose(View $view) {

        $subjects = Subject::whereSchoolId(School::schoolId())
            ->where('enabled', 1)
            ->pluck('name', 'id');

        $view->with([
            'subjects' => $subjects,
            'uris' => $this->uris()
        ]);

    }

}
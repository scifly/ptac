<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ScoreRangeComposer {

    use ModelTrait;

    public function compose(View $view) {

        $schoolId = School::schoolId();
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        array_unshift($subjects,'总分');
        $view->with([
            'subjects' => $subjects,
            'uris' => $this->uris()
        ]);

    }

}
<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class StudentAttendanceCountComposer {

    use ModelTrait;

    public function compose(View $view) {

        $schools = null;
        $grades = null;
        $classes = null;

        $schoolId = School::schoolId();
        $schools = School::whereId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        if ($schools) {
            $grades = Grade::whereSchoolId($schoolId)
                ->where('enabled', 1)
                ->pluck('name', 'id');
        }
        if ($grades) {
            $classes = Squad::whereGradeId($grades->keys()->first())
                ->where('enabled', 1)
                ->pluck('name', 'id');
        }

        $view->with([
            'schools' => $schools,
            'grades' => $grades,
            'classes' => $classes,
            'uris' => $this->uris()
        ]);

    }

}
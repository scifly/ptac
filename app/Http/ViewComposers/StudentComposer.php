<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class StudentComposer {

    use ModelTrait;

    public function compose(View $view) {

        $schoolId = School::schoolId();
        $grades = Grade::whereEnabled(1)
            ->where('school_id', $schoolId)
            ->pluck('name', 'id')
            ->toArray();
        $classes = Squad::whereEnabled(1)
            ->where('grade_id', array_keys($grades)[0])
            ->pluck('name', 'id')
            ->toArray();
        if (empty($classes)) {$classes[] = '' ;}
        if (empty($grades)) {$grades[] = '' ;}
        $view->with([
            'grades' => $grades,
            'classes' => $classes,
            'uris' => $this->uris()
        ]);

    }

}
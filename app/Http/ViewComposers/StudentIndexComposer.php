<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Contracts\View\View;

class StudentIndexComposer {

    use ControllerTrait;

    protected $user;

    public function __construct(User $user) {

        $this->user = $user;

    }

    public function compose(View $view) {

        $schools = null;
        $grades = null;
        $classes = null;
        $school = new School();
        $schoolId = $school->getSchoolId();
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
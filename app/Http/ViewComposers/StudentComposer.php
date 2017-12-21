<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Department;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use App\Models\User;
use Illuminate\Contracts\View\View;

class StudentComposer {
    
    use ControllerTrait;
    
    protected $user, $department;

    public function __construct(User $user, Department $department) {

        $this->user = $user;
        $this->department = $department;

    }

    public function compose(View $view) {

        $school = new School();
        $schoolId = $school->getSchoolId();
        $grades = Grade::whereEnabled(1)
            ->where('school_id', $schoolId)
            ->pluck('name', 'id')
            ->toArray();
        $classes = Squad::whereEnabled(1)
            ->where('grade_id', array_keys($grades)[0])
            ->pluck('name', 'id')
            ->toArray();
        $view->with([
            'schoolId' => $schoolId,
            'grades' => $grades,
            'classes' => $classes,
            'uris' => $this->uris()
        ]);

    }

}
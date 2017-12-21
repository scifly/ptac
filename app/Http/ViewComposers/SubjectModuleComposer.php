<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class SubjectModuleComposer {
    use ControllerTrait;
    protected $school;

    public function __construct(School $school) { $this->school = $school; }

    public function compose(View $view) {
        $schoolId = $this->school->getSchoolId();
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $view->with([
            'subjects' => $subjects,
            'uris' => $this->uris()

        ]);

    }

}
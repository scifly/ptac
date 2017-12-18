<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class MajorComposer {
    use ControllerTrait;
    protected $school, $subject;

    public function __construct(School $school, Subject $subject) {

        $this->school = $school;
        $this->subject = $subject;
    }

    public function compose(View $view) {

        $schoolId = $this->school->getSchoolId();
        $view->with([
            'schoolId' => $schoolId,
            'subjects' => $this->subject->where('school_id', $schoolId)
                ->pluck('name', 'id'),
            'uris' => $this->uris()

        ]);
    }
}
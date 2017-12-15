<?php
namespace App\Http\ViewComposers;

use App\Models\ExamType;
use App\Models\School;
use App\Models\Squad;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ExamComposer {

    protected $examtypes, $classes, $subjects, $school;

    public function __construct(Squad $classes, School $school) {
        $this->classes = $classes;
        $this->school = $school;
    }

    public function compose(View $view) {
        $schoolId = $this->school->getSchoolId();
        $school = School::find($schoolId);
        $examtypes = ExamType::whereSchoolId($schoolId)
            ->where('enabled',1)
            ->pluck('name', 'id');
        $squads = $school->classes
            ->where('enabled',1)
            ->pluck('name', 'id');
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $view->with([
            'examtypes' => $examtypes,
            'classes'   => $squads,
            'subjects'  => $subjects,
        ]);
    }

}
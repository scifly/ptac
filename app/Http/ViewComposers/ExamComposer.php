<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\ExamType;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ExamComposer {
    
    use ControllerTrait;

    public function compose(View $view) {
        
        $schoolId = School::id();
        $school = School::find($schoolId);
        
        $examtypes = ExamType::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $squads = $school->classes
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        
        $view->with([
            'examtypes' => $examtypes,
            'classes' => $squads,
            'subjects' => $subjects,
            'uris' => $this->uris()
        ]);
        
    }

}
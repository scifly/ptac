<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\ExamType;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ExamComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
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
            'classes'   => $squads,
            'subjects'  => $subjects,
            'uris'      => $this->uris(),
        ]);
        
    }
    
}
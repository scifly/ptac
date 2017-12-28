<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ScoreRangeComposer {
    
    use ControllerTrait;

    public function compose(View $view) {
        
        $schoolId = School::id();
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        
        $view->with([
            'schoolId' => $schoolId,
            'subjects' => $subjects,
            'uris' => $this->uris()
        ]);
        
    }
    
}
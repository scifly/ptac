<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class MajorComposer {
    
    use ControllerTrait;

    public function compose(View $view) {

        $schoolId = School::id();
        
        $view->with([
            'schoolId' => $schoolId,
            'subjects' => Subject::whereSchoolId($schoolId)->pluck('name', 'id'),
            'uris' => $this->uris()
        ]);
        
    }
    
}
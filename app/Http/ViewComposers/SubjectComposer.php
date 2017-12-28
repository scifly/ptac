<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Grade;
use App\Models\Major;
use App\Models\School;
use Illuminate\Contracts\View\View;

class SubjectComposer {
    
    use ControllerTrait;
    
    public function compose(View $view) {
        
        $schoolId = School::id();
        
        $view->with([
            'schoolId' => $schoolId,
            'grades' => Grade::whereSchoolId($schoolId)->pluck('name', 'id'),
            'majors' => Major::whereSchoolId($schoolId)->pluck('name', 'id'),
            'uris' => $this->uris()
        ]);
        
    }
    
}
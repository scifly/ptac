<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class SubjectModuleComposer {
    
    use ControllerTrait;

    public function compose(View $view) {
        
        $subjects = Subject::whereSchoolId(School::id())
            ->where('enabled', 1)
            ->pluck('name', 'id');
        
        $view->with([
            'subjects' => $subjects,
            'uris' => $this->uris()
        ]);

    }

}
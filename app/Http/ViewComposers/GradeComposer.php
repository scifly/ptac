<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Educator;
use App\Models\School;
use Illuminate\Contracts\View\View;

class GradeComposer {
    
    use ControllerTrait;

    public function compose(View $view) {

        $schoolId = School::id();
        
        $educators = Educator::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->get();
        $educatorUsers = [];
        foreach ($educators as $educator) {
            $educatorUsers[$educator->id] = $educator->user->realname;
        }
        
        $view->with([
            'schoolId' => $schoolId,
            'educators' => $educatorUsers,
            'uris' => $this->uris()
        ]);
        
    }

}
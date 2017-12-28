<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\School;
use Illuminate\Contracts\View\View;

class SquadComposer {
    
    use ControllerTrait;

    public function compose(View $view) {
        
        $schoolId = School::id();
        $grades = Grade::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $data = Educator::with('user')
            ->where('school_id', $schoolId)
            ->get()->toArray();
        $educators = [];
        if (!empty($data)) {
            foreach ($data as $v) {
                $educators[$v['id']] = $v['user']['realname'];
            }
        }
        
        $view->with([
            'grades' => $grades,
            'educators' => $educators,
            'uris' => $this->uris()
        ]);
        
    }

}
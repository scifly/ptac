<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Educator;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class EventIndexComposer {
    use ControllerTrait;

    protected $school;
    
    public function __construct(School $school) {
        $this->school = $school;
    }
    
    public function compose(View $view) {
        $schoolId = $this->school->getSchoolId();
        $educators = Educator::whereSchoolId($schoolId)
            ->where('enabled',1)
            ->get();
        $educatorUsers = [];
        foreach ($educators as $educator) {
            $educatorUsers[$educator->id] = $educator->user->realname;
        }
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled',1)
            ->pluck('name','id');
        $view->with([
            'educators' => $educatorUsers,
            'subjects'  => $subjects,
            'uris' => $this->uris()
        ]);
    }
}
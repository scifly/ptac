<?php
namespace App\Http\ViewComposers;

use App\Models\Group;
use App\Models\School;
use App\Models\Squad;
use App\Models\Subject;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\View\View;

class EducatorComposer {
    
    protected $school;
    
    public function __construct(School $school) {
        
        $this->school = $school;
    }
    
    public function compose(View $view) {
        $schoolId = $this->school->getSchoolId();
        /** @var School $school */
        $school = School::find($schoolId);
        
        $squads = $school->classes->where('enabled',1)
            ->pluck('name', 'id')->toArray();
        array_unshift($squads, '(请选择)');
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        $teams = Team::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        $groups = Group::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        array_unshift($subjects, '(请选择)');
        $view->with([
            'schoolId' => $schoolId,
            'squads'   => $squads,
            'subjects' => $subjects,
            'groups'   => $groups,
            'teams'    => $teams,
        ]);
    }
    
}
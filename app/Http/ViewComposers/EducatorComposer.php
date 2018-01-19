<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Group;
use App\Models\School;
use App\Models\Subject;
use App\Models\Team;
use Illuminate\Contracts\View\View;

class EducatorComposer {

    use ModelTrait;

    public function compose(View $view) {

        $schoolId = School::schoolId();
        $school = School::find($schoolId);

        $squads = $school->classes->where('enabled', 1)
            ->pluck('name', 'id')->toArray();
        $squads[0]='(请选择)';
        ksort($squads);
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        $teams = Team::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        $groups = Group::whereSchoolId($schoolId)
            ->orWhere('name', '学校')
            ->orWhere('name', '教职员工')
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        $subjects[0]='(请选择)';
        ksort($subjects);
        $view->with([
            'squads' => $squads,
            'subjects' => $subjects,
            'groups' => $groups,
            'teams' => $teams,
            'uris' => $this->uris()
        ]);
        
    }

}
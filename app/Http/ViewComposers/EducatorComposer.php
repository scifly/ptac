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

        $schoolId = School::id();
        $school = School::find($schoolId);

        $squads = $school->classes->where('enabled', 1)
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
            ->orWhere('name', '学校')
            ->orWhere('name', '教职员工')
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        array_unshift($subjects, '(请选择)');

        $view->with([
            'schoolId' => $schoolId,
            'squads' => $squads,
            'subjects' => $subjects,
            'groups' => $groups,
            'teams' => $teams,
            'uris' => $this->uris()
        ]);
        
    }

}
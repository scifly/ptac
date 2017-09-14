<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\Group;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\Subject;
use App\Models\Team;
use App\Models\User;
use App\Models\Squad;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class EducatorComposer {

    protected $user, $subject, $school, $squad, $group, $team;



    public function __construct(User $user, Subject $subject, School $school, Squad $squad, Group $group, Team $team) {

        $this->user = $user;
        $this->subject = $subject;
        $this->school = $school;
        $this->squad = $squad;
        $this->group = $group;
        $this->team = $team;

    }

    public function compose(View $view) {

        $view->with([
            'users' => $this->user->pluck('realname', 'id'),
            'schools' => $this->school->pluck('name', 'id'),
            'squads' => $this->squad->pluck('name','id'),
            'subjects' => $this->subject->pluck('name', 'id'),
            'groups' => $this->group->pluck('name', 'id'),
            'teams' => $this->team->pluck('name', 'id')

        ]);
    }

}
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
        $squads = $this->squad->pluck('name','id')->toArray();
        array_unshift($squads, '(请选择)');
        $subjects = $this->subject->pluck('name', 'id')->toArray();
        array_unshift($subjects, '(请选择)');
        $view->with([
            'users' => $this->user->pluck('realname', 'id'),
            'schools' => $this->school->pluck('name', 'id'),
            'squads' => $squads,
            'subjects' => $subjects,
            'groups' => $this->group->pluck('name', 'id'),
            'teams' => $this->team->pluck('name', 'id')

        ]);
    }

}
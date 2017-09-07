<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\Team;
use App\Models\User;
use App\Models\Squad;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class EducatorComposer {

    protected $users, $teams, $schools, $squads;



    public function __construct(User $users, Team $teams, School $schools, Squad $squads) {

        $this->users = $users;
        $this->teams = $teams;
        $this->schools = $schools;
        $this->squads = $squads;

    }

    public function compose(View $view) {

        $view->with([
            'users' => $this->users->pluck('username', 'id'),
            'schools' => $this->schools->pluck('name', 'id'),
            'squads' => $this->squads->pluck('name','id'),
        ]);
    }

}
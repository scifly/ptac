<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\Team;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class EducatorComposer {

    protected $users;
    protected $teams;
    protected $schools;

    public function __construct(User $users, Team $teams, School $schools) {

        $this->users = $users;
        $this->teams = $teams;
        $this->schools = $schools;

    }

    public function compose(View $view) {

        $view->with([
            'users' => $this->users->pluck('username', 'id'),
            'teams' => $this->teams->pluck('name', 'id'),
            'schools' => $this->schools->pluck('name', 'id'),
        ]);
    }

}
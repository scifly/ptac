<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Educator;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class WapSiteComposer {

    protected $schools;

    public function __construct(School $schools) {

        $this->schools = $schools;

    }

    public function compose(View $view) {


        $view->with([
            'schools' => $this->schools->pluck('name', 'id'),
        ]);
    }

}
<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Educator;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\User;
use App\Models\WapSite;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class WapSiteModuleComposer {

    protected $school;
    protected $wapSite;

    public function __construct(School $school, WapSite $wapSite) {

        $this->school = $school;
        $this->wapSite = $wapSite;

    }

    public function compose(View $view) {


        $view->with([
            'schools' => $this->school->pluck('name', 'id'),
            'wapSites' => $this->wapSite->pluck('site_title', 'id'),
        ]);
    }

}
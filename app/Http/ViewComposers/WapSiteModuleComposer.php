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

    protected $wapSite;

    public function __construct(WapSite $wapSite) {

        $this->wapSite = $wapSite;

    }

    public function compose(View $view) {

        $view->with([
            'wapSites' => $this->wapSite->pluck('site_title', 'id'),
        ]);
    }

}
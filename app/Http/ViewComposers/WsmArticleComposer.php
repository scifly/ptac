<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Educator;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\User;
use App\Models\WapSite;
use App\Models\WapSiteModule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class WsmArticleComposer {

    protected $wsms;

    public function __construct(WapSiteModule $wsms) {

        $this->wsms = $wsms;

    }

    /**
     * @param View $view
     */
    public function compose(View $view) {
        $view->with([
            'wsms' => $this->wsms->pluck('name', 'id'),
        ]);
    }

}
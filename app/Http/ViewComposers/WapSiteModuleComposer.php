<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\WapSite;
use Illuminate\Contracts\View\View;

class WapSiteModuleComposer {
    use ControllerTrait;
    protected $wapSite;

    public function __construct(WapSite $wapSite) {

        $this->wapSite = $wapSite;

    }

    public function compose(View $view) {

        $view->with([
            'wapSites' => $this->wapSite->pluck('site_title', 'id'),
            'uris' => $this->uris()

        ]);
    }

}
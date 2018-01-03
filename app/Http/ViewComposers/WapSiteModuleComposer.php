<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\WapSite;
use Illuminate\Contracts\View\View;

class WapSiteModuleComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'wapSites' => WapSite::pluck('site_title', 'id'),
            'uris' => $this->uris()
        ]);
        
    }

}
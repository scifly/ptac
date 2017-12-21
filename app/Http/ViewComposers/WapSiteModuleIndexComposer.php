<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\WapSite;
use Illuminate\Contracts\View\View;

class WapSiteModuleIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);
    }

}
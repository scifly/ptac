<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

class WapSiteModuleIndexComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);
        
    }

}
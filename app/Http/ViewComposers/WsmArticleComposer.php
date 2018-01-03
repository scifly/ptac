<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\WapSiteModule;
use Illuminate\Contracts\View\View;

class WsmArticleComposer {
    
    use ModelTrait;

    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'wsms' => WapSiteModule::pluck('name', 'id'),
            'uris' => $this->uris()
        ]);
        
    }

}
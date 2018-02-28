<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\WapSite;
use App\Models\WapSiteModule;
use Illuminate\Contracts\View\View;

class WsmArticleComposer {
    
    use ModelTrait;

    /**
     * @param View $view
     */
    public function compose(View $view) {
        $schoolId = $this->schoolId();

        $view->with([
            'wsms' => WapSiteModule::
                    whereWapSiteId(WapSite::whereSchoolId($schoolId)->first()->id)
                    ->pluck('name', 'id'),
            'uris' => $this->uris()
        ]);
        
    }

}
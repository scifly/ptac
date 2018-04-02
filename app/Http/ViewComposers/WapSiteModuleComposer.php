<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\WapSite;
use Illuminate\Contracts\View\View;

class WapSiteModuleComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        $schoolId = $this->schoolId();
        $view->with([
            'wapSites' => WapSite::whereSchoolId($schoolId)->pluck('site_title', 'id'),
            'uris'     => $this->uris(),
        ]);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\WapSite;
use App\Models\WapSiteModule;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class WapSiteModuleComposer
 * @package App\Http\ViewComposers
 */
class WapSiteModuleComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $media = null;
        if (Request::route('id')) {
            $media = WapSiteModule::find(Request::route('id'))->media;
        }
        $view->with([
            'wapSites' => WapSite::whereSchoolId($this->schoolId())->pluck('site_title', 'id'),
            'media'    => $media,
        ]);
        
    }
    
}
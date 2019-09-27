<?php
namespace App\Http\ViewComposers;

use App\Models\WapSiteModule;
use Illuminate\Contracts\View\View;

/**
 * Class WapSiteModuleComposer
 * @package App\Http\ViewComposers
 */
class WapSiteModuleComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new WapSiteModule)->compose()
        );
        
    }
    
}
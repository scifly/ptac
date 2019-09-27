<?php
namespace App\Http\ViewComposers;

use App\Models\WapSite;
use Illuminate\Contracts\View\View;

/**
 * Class WapSiteComposer
 * @package App\Http\ViewComposers
 */
class WapSiteComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new WapSite)->compose()
        );
        
    }
    
}
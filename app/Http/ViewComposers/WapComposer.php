<?php
namespace App\Http\ViewComposers;

use App\Models\Wap;
use Illuminate\Contracts\View\View;

/**
 * Class WapComposer
 * @package App\Http\ViewComposers
 */
class WapComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new Wap)->compose()
        );
        
    }
    
}
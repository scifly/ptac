<?php
namespace App\Http\ViewComposers;

use App\Models\PassageLog;
use Illuminate\Contracts\View\View;

/**
 * Class PassageLogComposer
 * @package App\Http\ViewComposers
 */
class PassageLogComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new PassageLog)->compose()
        );
        
    }
    
}
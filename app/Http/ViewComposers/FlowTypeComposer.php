<?php
namespace App\Http\ViewComposers;

use App\Models\FlowType;
use Illuminate\Contracts\View\View;

/**
 * Class FlowTypeComposer
 * @package App\Http\ViewComposers
 */
class FlowTypeComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new FlowType)->compose()
        );
        
    }
    
}
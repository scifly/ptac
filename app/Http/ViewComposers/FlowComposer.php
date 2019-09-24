<?php
namespace App\Http\ViewComposers;

use App\Models\FlowType;
use Illuminate\Contracts\View\View;

/**
 * Class FlowComposer
 * @package App\Http\ViewComposers
 */
class FlowComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            (new FlowType)->compose()
        );
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Models\Flow;
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
            (new Flow)->compose()
        );
        
    }
    
}
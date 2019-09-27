<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use Illuminate\Contracts\View\View;

/**
 * Class ActionComposer
 * @package App\Http\ViewComposers
 */
class ActionComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            (new Action)->compose()
        );
        
    }
    
}
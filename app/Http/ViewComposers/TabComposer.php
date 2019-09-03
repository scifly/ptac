<?php
namespace App\Http\ViewComposers;

use App\Models\Tab;
use Illuminate\Contracts\View\View;

/**
 * Class TabComposer
 * @package App\Http\ViewComposers
 */
class TabComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            (new Tab)->compose()
        );
        
    }
    
}
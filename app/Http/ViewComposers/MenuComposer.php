<?php
namespace App\Http\ViewComposers;

use App\Models\Menu;
use Illuminate\Contracts\View\View;

/**
 * Class MenuComposer
 * @package App\Http\ViewComposers
 */
class MenuComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new Menu)->compose()
        );
        
    }
    
}
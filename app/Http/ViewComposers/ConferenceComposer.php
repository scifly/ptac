<?php
namespace App\Http\ViewComposers;

use App\Models\Conference;
use Illuminate\Contracts\View\View;

/**
 * Class ConferenceComposer
 * @package App\Http\ViewComposers
 */
class ConferenceComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            (new Conference)->compose()
        );
        
    }
    
}
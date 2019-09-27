<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use Illuminate\Contracts\View\View;
use Throwable;

/**
 * Class SchoolComposer
 * @package App\Http\ViewComposers
 */
class SchoolComposer {
    
    /**
     * @param View $view
     * @throws Throwable
     */
    public function compose(View $view) {
        
        $view->with(
            (new School)->compose()
        );
        
    }
    
}
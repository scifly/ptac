<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class SchoolComposer
 * @package App\Http\ViewComposers
 */
class SchoolComposer {
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
    
        $view->with(
            (new School)->compose()
        );
        
    }
    
}
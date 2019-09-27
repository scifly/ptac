<?php
namespace App\Http\ViewComposers;

use App\Models\Grade;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class GradeComposer
 * @package App\Http\ViewComposers
 */
class GradeComposer {
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
        
        $view->with(
            (new Grade)->compose()
        );
        
    }
    
}
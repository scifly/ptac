<?php
namespace App\Http\ViewComposers;

use App\Models\Subject;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class SubjectComposer
 * @package App\Http\ViewComposers
 */
class SubjectComposer {
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
        
        $view->with(
            (new Subject)->compose()
        );
        
    }
    
}
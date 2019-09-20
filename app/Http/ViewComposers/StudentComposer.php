<?php
namespace App\Http\ViewComposers;

use App\Models\Student;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class StudentComposer
 * @package App\Http\ViewComposers
 */
class StudentComposer {
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
        
        $view->with(
            (new Student)->compose()
        );
        
    }
    
}
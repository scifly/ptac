<?php
namespace App\Http\ViewComposers;

use App\Models\Exam;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class ExamComposer
 * @package App\Http\ViewComposers
 */
class ExamComposer {
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
    
        $view->with(
            (new Exam)->compose()
        );
        
    }
    
}
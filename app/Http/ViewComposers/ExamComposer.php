<?php
namespace App\Http\ViewComposers;

use App\Models\Exam;
use Illuminate\Contracts\View\View;

/**
 * Class ExamComposer
 * @package App\Http\ViewComposers
 */
class ExamComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            (new Exam)->compose()
        );
        
    }
    
}
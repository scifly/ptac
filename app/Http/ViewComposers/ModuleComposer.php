<?php
namespace App\Http\ViewComposers;

use App\Models\Module;
use Illuminate\Contracts\View\View;

/**
 * Class ExamIndexComposer
 * @package App\Http\ViewComposers
 */
class ModuleComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new Module)->compose()
        );
        
    }
    
}
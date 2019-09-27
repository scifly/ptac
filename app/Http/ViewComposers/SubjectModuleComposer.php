<?php
namespace App\Http\ViewComposers;

use App\Models\SubjectModule;
use Illuminate\Contracts\View\View;

/**
 * Class SubjectModuleComposer
 * @package App\Http\ViewComposers
 */
class SubjectModuleComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new SubjectModule)->compose()
        );
        
    }
    
}
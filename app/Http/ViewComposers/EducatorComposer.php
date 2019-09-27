<?php
namespace App\Http\ViewComposers;

use App\Models\Educator;
use Illuminate\Contracts\View\View;
use Throwable;

/**
 * Class EducatorComposer
 * @package App\Http\ViewComposers
 */
class EducatorComposer {
    
    /**
     * @param View $view
     * @throws Throwable
     */
    public function compose(View $view) {
        
        $view->with(
            (new Educator)->compose()
        );
        
    }
    
}
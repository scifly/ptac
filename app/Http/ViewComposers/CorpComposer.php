<?php
namespace App\Http\ViewComposers;

use App\Models\Corp;
use Illuminate\Contracts\View\View;
use Throwable;

/**
 * Class CorpComposer
 * @package App\Http\ViewComposers
 */
class CorpComposer {
    
    /**
     * @param View $view
     * @throws Throwable
     */
    public function compose(View $view) {
        
        $view->with(
            (new Corp)->compose()
        );
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\App;
use Illuminate\Contracts\View\View;

/**
 * Class AppComposer
 * @package App\Http\ViewComposers
 */
class AppComposer {
    
    use ModelTrait;
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new App)->compose()
        );
        
    }
    
}
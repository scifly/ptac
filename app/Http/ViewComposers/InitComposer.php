<?php
namespace App\Http\ViewComposers;

use App\Helpers\Configure;
use Illuminate\Contracts\View\View;
use ReflectionException;

/**
 * Class InitComposer
 * @package App\Http\ViewComposers
 */
class InitComposer {
    
    /**
     * @param View $view
     * @throws ReflectionException
     */
    public function compose(View $view) {
        
        $view->with(
            (new Configure)->compose()
        );
        
    }
    
}

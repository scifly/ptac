<?php
namespace App\Http\ViewComposers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Throwable;

/**
 * Class OperatorComposer
 * @package App\Http\ViewComposers
 */
class OperatorComposer {
    
    /**
     * @param View $view
     * @throws Throwable
     */
    public function compose(View $view) {
        
        $view->with(
            (new User)->compose()
        );
        
    }
    
}
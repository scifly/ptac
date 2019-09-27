<?php
namespace App\Http\ViewComposers;

use App\Models\User;
use Illuminate\Contracts\View\View;
use Throwable;

/**
 * Class UserComposer
 * @package App\Http\ViewComposers
 */
class UserComposer {
    
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
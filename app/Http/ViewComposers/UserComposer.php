<?php
namespace App\Http\ViewComposers;

use App\Models\User;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class UserComposer
 * @package App\Http\ViewComposers
 */
class UserComposer {
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
        
        $view->with(
            (new User)->compose()
        );
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

/**
 * Class UserResetComposer
 * @package App\Http\ViewComposers
 */
class UserResetComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'disabled' => true
        ]);
        
    }
    
}
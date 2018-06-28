<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;

class UserResetComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'disabled' => true
        ]);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Models\Mobile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UserResetComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'disabled' => true
        ]);
        
    }
    
}
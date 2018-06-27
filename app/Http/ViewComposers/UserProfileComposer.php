<?php
namespace App\Http\ViewComposers;

use App\Models\Mobile;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class UserProfileComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'mobile' => Mobile::whereUserId(Auth::id())->where('isdefault', 1)->first()->mobile,
            'disabled' => true
        ]);
        
    }
    
}
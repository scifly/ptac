<?php
namespace App\Http\ViewComposers;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserEditComposer
 * @package App\Http\ViewComposers
 */
class UserEditComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $user = User::find(Auth::id());
        $view->with([
            'mobile' => $user->mobiles->isNotEmpty()
                ? $user->mobiles->where('isdefault', 1)->first()->mobile
                : '(n/a)',
            'disabled' => true
        ]);
        
    }
    
}
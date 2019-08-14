<?php
namespace App\Http\ViewComposers;

use App\Models\User;
use Illuminate\Contracts\View\View;

/**
 * Class UserComposer
 * @package App\Http\ViewComposers
 */
class UserComposer {
    
    protected $user;
    
    /**
     * UserComposer constructor.
     * @param User $user
     */
    function __construct(User $user) {
        
        $this->user = $user;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            $this->user->compose()
        );
        
    }
    
}
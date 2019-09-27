<?php
namespace App\Http\ViewComposers;

use App\Models\Message;
use Illuminate\Contracts\View\View;
use Throwable;

/**
 * Class MessageComposer
 * @package App\Http\ViewComposers
 */
class MessageComposer {
    
    /**
     * @param View $view
     * @throws Throwable
     */
    public function compose(View $view) {
        
        $view->with(
            (new Message)->compose()
        );
        
    }
    
}
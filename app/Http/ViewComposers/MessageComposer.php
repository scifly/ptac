<?php
namespace App\Http\ViewComposers;

use App\Models\Message;
use Illuminate\Contracts\View\View;

/**
 * Class MessageComposer
 * @package App\Http\ViewComposers
 */
class MessageComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            (new Message)->compose()
        );
    
    }
    
}
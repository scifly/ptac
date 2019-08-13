<?php
namespace App\Http\ViewComposers;

use App\Models\Message;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

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
            (new Message)->compose(Request::path())
        );
    
    }
    
}
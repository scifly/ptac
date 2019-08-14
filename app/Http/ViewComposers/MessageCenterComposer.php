<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Message;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class MessageCenterComposer
 * @package App\Http\ViewComposers
 */
class MessageCenterComposer {
    
    use ModelTrait;
    
    protected $message;
    
    /**
     * MessageCenterComposer constructor.
     * @param Message $message
     */
    function __construct(Message $message) {
        
        $this->message = $message;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            $this->message->compose(
                join('/', array_slice(explode('/', Request::route()->uri), 1))
            )
        );
        
    }
    
}
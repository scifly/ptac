<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Message;
use Illuminate\Contracts\View\View;

/**
 * Class MessageCenterIndexComposer
 * @package App\Http\ViewComposers
 */
class MessageCenterIndexComposer {
    
    use ModelTrait;
    
    protected $message;
    
    /**
     * MessageCenterIndexComposer constructor.
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
            array_combine(
                ['messageTypes', 'mediaTypes', 'acronym', 'canSend'],
                $this->message->compose()
            )
        );
        
    }
    
}
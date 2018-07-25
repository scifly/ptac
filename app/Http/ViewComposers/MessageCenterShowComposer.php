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
class MessageCenterShowComposer {
    
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
        
        $id = Request::route('id');
        $message = Message::find($id);
        $view->with([
            'content' => $this->message->detail($id),
            'replies' => $this->message->replies($id, $message->msl_id)
        ]);
        
    }
    
}
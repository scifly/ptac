<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Message;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Log;
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
        $detail = $id ? $this->message->detail($id) : null;
        Log::info('content', json_decode($detail[$detail['type']], true));
        $view->with([
            'detail'  => $detail,
            'content' => json_decode($detail[$detail['type']], true),
            'replies' => $id ? $this->message->replies($id, $this->message->find($id)->msl_id) : null,
        ]);
        
    }
    
}
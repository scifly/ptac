<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Message;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
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
    
        $user = Auth::user();
        $data = $this->message->compose(
            join('/', array_slice(explode('/', Request::route()->uri), 1))
        );
        $data = array_merge(
            $data, ['userid' => json_decode($user->ent_attrs, true)['userid']]
        );
        $view->with($data);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Department;
use App\Models\Grade;
use App\Models\Message;
use App\Models\MessageType;
use App\Models\School;
use App\Models\Squad;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * Class MessageCenterCreateComposer
 * @package App\Http\ViewComposers
 */
class MessageCenterShowComposer {
    
    use ModelTrait;
    
    /**
     * MessageCenterShowComposer constructor.
     */
    function __construct() { }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        list($content, $edit) = (new Message)->detail(
            Request::route('id')
        );
        
        $view->with([
            'content' => $content,
            'edit'    => $edit,
            'show'    => true,
        ]);
        
    }
    
}
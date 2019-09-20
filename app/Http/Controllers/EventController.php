<?php
namespace App\Http\Controllers;

use App\Models\Event;

/**
 * 日历
 *
 * Class EventController
 * @package App\Http\Controllers
 */
class EventController extends Controller {
    
    protected $event;
    
    /**
     * EventController constructor.
     * @param Event $event
     */
    function __construct(Event $event) {
        
        $this->middleware(['auth', 'checkrole']);
        $this->event = $event;
        
    }
    
}

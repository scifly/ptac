<?php
namespace App\Http\ViewComposers;

use App\Models\Room;
use Illuminate\Contracts\View\View;

/**
 * Class RoomComposer
 * @package App\Http\ViewComposers
 */
class RoomComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new Room)->compose()
        );
        
    }
    
}
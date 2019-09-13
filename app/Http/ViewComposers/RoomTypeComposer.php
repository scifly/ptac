<?php
namespace App\Http\ViewComposers;

use App\Models\RoomType;
use Illuminate\Contracts\View\View;

/**
 * Class RoomTypeComposer
 * @package App\Http\ViewComposers
 */
class RoomTypeComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            (new RoomType)->compose()
        );
        
    }
    
}
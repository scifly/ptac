<?php
namespace App\Http\ViewComposers;

use App\Models\Camera;
use Illuminate\Contracts\View\View;

/**
 * Class CameraComposer
 * @package App\Http\ViewComposers
 */
class CameraComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
       $view->with(
           (new Camera)->compose()
       );
        
    }
    
}
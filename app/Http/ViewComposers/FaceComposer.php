<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Face;
use Illuminate\Contracts\View\View;

/**
 * Class FaceComposer
 * @package App\Http\ViewComposers
 */
class FaceComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            (new Face)->compose()
        );
        
    }
    
}
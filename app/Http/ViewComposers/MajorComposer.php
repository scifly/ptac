<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Major;
use Illuminate\Contracts\View\View;

/**
 * Class MajorComposer
 * @package App\Http\ViewComposers
 */
class MajorComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            (new Major)->compose()
        );
        
    }
    
}
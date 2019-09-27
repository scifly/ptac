<?php
namespace App\Http\ViewComposers;

use App\Models\Bed;
use Illuminate\Contracts\View\View;
use ReflectionException;

/**
 * Class BuildingComposer
 * @package App\Http\ViewComposers
 */
class BedComposer {
    
    /**
     * @param View $view
     * @throws ReflectionException
     */
    public function compose(View $view) {
        
        $view->with(
            (new Bed)->compose()
        );
        
    }
    
}
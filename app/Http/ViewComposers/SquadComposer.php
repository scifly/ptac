<?php
namespace App\Http\ViewComposers;

use App\Models\Squad;
use Illuminate\Contracts\View\View;
use ReflectionException;

/**
 * Class SquadComposer
 * @package App\Http\ViewComposers
 */
class SquadComposer {
    
    /**
     * @param View $view
     * @throws ReflectionException
     */
    public function compose(View $view) {
    
        $view->with(
            (new Squad)->compose()
        );
        
    }
    
}
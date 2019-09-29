<?php
namespace App\Http\ViewComposers;

use App\Models\Consumption;
use Exception;
use Illuminate\Contracts\View\View;
use ReflectionException;

/**
 * Class ConsumptionComposer
 * @package App\Http\ViewComposers
 */
class ConsumptionComposer {
    
    /**
     * @param View $view
     * @throws ReflectionException
     * @throws Exception
     */
    public function compose(View $view) {
        
        $view->with(
            (new Consumption)->compose()
        );
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Consumption;
use Exception;
use Illuminate\Contracts\View\View;
use ReflectionException;

/**
 * Class ConsumptionComposer
 * @package App\Http\ViewComposers
 */
class ConsumptionComposer {
    
    use ModelTrait;
    
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
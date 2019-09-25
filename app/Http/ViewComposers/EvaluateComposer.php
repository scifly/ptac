<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Evaluate;
use Illuminate\Contracts\View\View;
use ReflectionException;

/**
 * Class EvaluateComposer
 * @package App\Http\ViewComposers
 */
class EvaluateComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     * @throws ReflectionException
     */
    public function compose(View $view) {
    
        $view->with([
            (new Evaluate)->compose()
        ]);
        
    }
    
}
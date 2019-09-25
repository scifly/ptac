<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\User;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class OperatorComposer
 * @package App\Http\ViewComposers
 */
class OperatorComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
    
        $view->with(
            (new User)->compose()
        );
        
    }
    
}
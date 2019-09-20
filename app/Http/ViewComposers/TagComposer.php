<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Tag;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class TagComposer
 * @package App\Http\ViewComposers
 */
class TagComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
    
        $view->with(
            (new Tag)->compose()
        );
        
    }
    
}
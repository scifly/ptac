<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Template;
use Illuminate\Contracts\View\View;

/**
 * Class TemplateComposer
 * @package App\Http\ViewComposers
 */
class TemplateComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            (new Template)->compose()
        );
        
    }
    
}
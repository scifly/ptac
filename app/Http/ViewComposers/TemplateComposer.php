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
    
    protected $template;
    
    /**
     * AppComposer constructor.
     * @param Template $template
     */
    function __construct(Template $template) {
        
        $this->template = $template;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            $this->template->compose()
        );
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Module;
use Illuminate\Contracts\View\View;

/**
 * Class ExamIndexComposer
 * @package App\Http\ViewComposers
 */
class ModuleComposer {
    
    use ModelTrait;
    
    protected $module;
    
    /**
     * ModuleComposer constructor.
     * @param Module $module
     */
    function __construct(Module $module) {
        
        $this->module = $module;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            $this->module->compose()
        );
        
    }
    
}
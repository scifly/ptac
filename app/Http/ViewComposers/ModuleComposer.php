<?php
namespace App\Http\ViewComposers;

use App\Models\Module;
use Illuminate\Contracts\View\View;

/**
 * Class ExamIndexComposer
 * @package App\Http\ViewComposers
 */
class ModuleComposer {
    
    
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
        
        list($schools, $groups, $tabs, $media) = $this->module->compose();
        
        $view->with([
            'schools' => $schools,
            'groups' => $groups,
            'tabs' => $tabs,
            'media' => $media
        ]);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\Configure;
use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;
use ReflectionException;

/**
 * Class InitComposer
 * @package App\Http\ViewComposers
 */
class InitComposer {
    
    use ModelTrait;
    
    protected $config;
    
    /**
     * InitComposer constructor.
     * @param Configure $config
     */
    function __construct(Configure $config) {
        
        $this->config = $config;
        
    }
    
    /**
     * @param View $view
     * @throws ReflectionException
     */
    public function compose(View $view) {
        
        
        $view->with(
            array_combine(
                ['params', 'list'],
                $this->config->compose()
            )
        );
        
    }
    
}

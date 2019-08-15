<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\App;
use Illuminate\Contracts\View\View;

/**
 * Class AppComposer
 * @package App\Http\ViewComposers
 */
class AppComposer {
    
    use ModelTrait;
    
    protected $app;
    
    /**
     * AppComposer constructor.
     * @param App $app
     */
    function __construct(App $app) {
        
        $this->app = $app;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            $this->app->compose()
        );
        
    }
    
}
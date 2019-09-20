<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Educator;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class EducatorComposer
 * @package App\Http\ViewComposers
 */
class EducatorComposer {
    
    use ModelTrait;
    
    protected $educator;
    
    /**
     * EducatorComposer constructor.
     * @param Educator $educator
     */
    function __construct(Educator $educator) {
        
        $this->educator = $educator;
        
    }
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
    
        $view->with(
            $this->educator->compose()
        );
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

/**
 * Class SchoolComposer
 * @package App\Http\ViewComposers
 */
class SchoolComposer {
    
    use ModelTrait;
    
    protected $school;
    
    /**
     * SchoolComposer constructor.
     * @param School $school
     */
    function __construct(School $school) {
        
        $this->school = $school;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            $this->school->compose()
        );
        
    }
    
}
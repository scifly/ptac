<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Custodian;
use App\Models\Educator;
use Illuminate\Contracts\View\View;

/**
 * Class EducatorComposer
 * @package App\Http\ViewComposers
 */
class EducatorComposer {
    
    use ModelTrait;
    
    protected $educator, $custodian;
    
    /**
     * EducatorComposer constructor.
     * @param Educator $educator
     * @param Custodian $custodian
     */
    function __construct(Educator $educator, Custodian $custodian) {
        
        $this->educator = $educator;
        $this->custodian = $custodian;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            array_combine(
                [
                    'squads', 'subjects', 'groups', 'selectedDepartmentIds',
                    'selectedDepartments', 'mobiles'
                ],
                $this->educator->compose()
            )
        );
        
    }
    
}
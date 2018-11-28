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
        
        list($squads, $subjects, $groups, $departmentIds, $departments, $mobiles) = $this->educator->compose();
        $firstOption = [0 => '(请选择)'];
        $view->with([
            'squads'                => $firstOption + $squads,
            'subjects'              => $firstOption + $subjects,
            'groups'                => $firstOption + $groups,
            'mobiles'               => $mobiles,
            'selectedDepartmentIds' => $departmentIds,
            'selectedDepartments'   => $departments,
        ]);
        
    }
    
}
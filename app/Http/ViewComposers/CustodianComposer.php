<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Custodian;
use App\Models\CustodianStudent;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class CustodianComposer
 * @package App\Http\ViewComposers
 */
class CustodianComposer {
    
    use ModelTrait;
    
    protected $custodian, $educator;
    
    /**
     * CustodianComposer constructor.
     * @param Custodian $custodian
     * @param Educator $educator
     */
    function __construct(Custodian $custodian, Educator $educator) {
        
        $this->custodian = $custodian;
        $this->educator = $educator;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        list($title, $grades, $classes, $students, $relations, $mobiles) = $this->custodian->compose();
        list($squads, $subjects) = $this->educator->compose();
        $firstOption = [0 => '(请选择)'];
        
        $view->with([
            'grades'       => $grades,
            'classes'      => $classes,
            'students'     => $students,
            'mobiles'      => $mobiles,
            'relations'    => $relations,
            'squads'       => $firstOption + $squads,
            'subjects'     => $firstOption + $subjects,
            'title'        => $title,
            'relationship' => true,
        ]);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\{Custodian, Educator};
use Illuminate\Contracts\View\View;

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
        
        $view->with([
            'grades'       => $grades,
            'classes'      => $classes,
            'students'     => $students,
            'mobiles'      => $mobiles,
            'relations'    => $relations,
            'title'        => $title,
            'relationship' => true,
        ]);
        
    }
    
}
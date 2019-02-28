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
    
        $view->with(
            array_combine(
                [
                    'custodian', 'title', 'grades', 'classes', 'students',
                    'relations', 'mobiles', 'relationship'
                ],
                array_merge($this->custodian->compose(), [true])
            )
        );
        
    }
    
}
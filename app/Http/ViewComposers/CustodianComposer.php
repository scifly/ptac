<?php
namespace App\Http\ViewComposers;

use App\Models\Custodian;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class CustodianComposer
 * @package App\Http\ViewComposers
 */
class CustodianComposer {
    
    protected $custodian;
    
    /**
     * CustodianComposer constructor.
     * @param Custodian $custodian
     */
    function __construct(Custodian $custodian) {
        
        $this->custodian = $custodian;
        
    }
    
    /**
     * @param View $view
     * @return void
     * @throws Exception
     */
    public function compose(View $view) {
        
        $view->with(
            $this->custodian->compose()
        );
        
    }
    
}
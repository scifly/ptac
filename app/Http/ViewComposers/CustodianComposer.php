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
    
    /**
     * @param View $view
     * @return void
     * @throws Exception
     */
    public function compose(View $view) {
        
        $view->with(
            (new Custodian)->compose()
        );
        
    }
    
}
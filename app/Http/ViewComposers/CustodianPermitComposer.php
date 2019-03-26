<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Card;
use Illuminate\Contracts\View\View;

/**
 * Class CustodianPermitComposer
 * @package App\Http\ViewComposers
 */
class CustodianPermitComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            (new Card)->compose('Custodian')
        );
    
    }
    
}
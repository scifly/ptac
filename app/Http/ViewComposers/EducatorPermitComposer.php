<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Card;
use Illuminate\Contracts\View\View;

/**
 * Class CustodianIssueComposer
 * @package App\Http\ViewComposers
 */
class EducatorPermitComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            (new Card)->compose('Educator')
        );
        
    }
    
}
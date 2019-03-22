<?php
namespace App\Http\ViewComposers;

use App\Models\PassageRule;
use Illuminate\Contracts\View\View;

/**
 * Class PassageRuleComposer
 * @package App\Http\ViewComposers
 */
class PassageRuleComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            array_combine(
                ['pr', 'weekdays', 'trs', 'doors', 'selectedDoors'],
                (new PassageRule)->compose()
            )
        );
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Models\Group;
use Illuminate\Contracts\View\View;

/**
 * Class GroupComposer
 * @package App\Http\ViewComposers
 */
class GroupComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new Group)->compose()
        );
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Models\Poll;
use Illuminate\Contracts\View\View;

/**
 * Class PollComposer
 * @package App\Http\ViewComposers
 */
class PollComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new Poll)->compose()
        );
        
    }
    
}
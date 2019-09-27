<?php
namespace App\Http\ViewComposers;

use App\Models\PollTopic;
use Illuminate\Contracts\View\View;

/**
 * Class PollTopicComposer
 * @package App\Http\ViewComposers
 */
class PollTopicComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            (new PollTopic)->compose()
        );
        
    }
    
}
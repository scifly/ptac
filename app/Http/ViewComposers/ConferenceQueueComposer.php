<?php
namespace App\Http\ViewComposers;

use App\Models\ConferenceQueue;
use Illuminate\Contracts\View\View;

/**
 * Class ConferenceQueueComposer
 * @package App\Http\ViewComposers
 */
class ConferenceQueueComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            (new ConferenceQueue)->compose()
        );
        
    }
    
}
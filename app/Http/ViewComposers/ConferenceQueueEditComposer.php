<?php
namespace App\Http\ViewComposers;

use App\Models\ConferenceQueue;
use App\Models\Educator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ConferenceQueueEditComposer
 * @package App\Http\ViewComposers
 */
class ConferenceQueueEditComposer {
    
    protected $educator;
    
    /**
     * ConferenceQueueEditComposer constructor.
     * @param Educator $educator
     */
    function __construct(Educator $educator) { $this->educator = $educator; }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'selectedEducators' => $this->educator->educatorList(
                ConferenceQueue::find(Request::route('id'))->educator_ids),
        ]);
        
    }
    
}
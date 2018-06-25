<?php
namespace App\Http\ViewComposers;

use App\Models\ConferenceQueue;
use App\Models\Educator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class ConferenceQueueEditComposer {
    
    protected $educator;
    
    function __construct(Educator $educator) { $this->educator = $educator; }
    
    public function compose(View $view) {
        
        $view->with([
            'selectedEducators' => $this->educator->educatorList(
                ConferenceQueue::find(Request::route('id'))->educator_ids),
        ]);
        
    }
    
}
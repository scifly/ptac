<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\ConferenceQueue;
use App\Models\ConferenceRoom;
use App\Models\Educator;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class ConferenceQueueEditComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'selectedEducators' => Educator::educatorList(
                ConferenceQueue::find(Request::route('id'))->educator_ids),
            'uris' => $this->uris()
        ]);

    }

}
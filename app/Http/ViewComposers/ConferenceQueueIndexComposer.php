<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\ConferenceRoom;
use App\Models\Educator;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ConferenceQueueIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);

    }

}
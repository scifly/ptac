<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class ConferenceRoomComposer {
    use ControllerTrait;

    public function compose(View $view) {
        
        $view->with([
            'schoolId' => School::id(),
            'uris' => $this->uris()
        ]);
        
    }
    
}
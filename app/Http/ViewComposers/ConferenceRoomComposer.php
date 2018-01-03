<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class ConferenceRoomComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'schoolId' => School::id(),
            'uris' => $this->uris()
        ]);

    }

}
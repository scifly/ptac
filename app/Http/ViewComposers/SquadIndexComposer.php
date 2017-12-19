<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Educator;
use App\Models\Grade;
use App\Models\School;
use Illuminate\Contracts\View\View;

class SquadIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {

        $view->with([
            'uris' => $this->uris()
        ]);
    }

}
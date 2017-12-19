<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Action;
use App\Models\ActionType;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class ActionIndexComposer {
    
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);

    }

}
<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Action;
use App\Models\School;
use App\Models\Tab;
use Illuminate\Contracts\View\View;

class MessageTypeIndexComposer {

    use ControllerTrait;
    
    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);
    }

}
<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\App;
use App\Models\CommType;
use App\Models\MessageType;
use App\Models\User;
use Illuminate\Contracts\View\View;

class MessageIndexComposer {
    
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);
        
    }

}
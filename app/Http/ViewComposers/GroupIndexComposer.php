<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use Illuminate\Contracts\View\View;

class GroupIndexComposer {
    
    use ControllerTrait;

    public function compose(View $view) {
        
        $view->with(['uris' => $this->uris()]);
        
    }

}
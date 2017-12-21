<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class CorpIndexComposer {
    
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);
        
    }

}
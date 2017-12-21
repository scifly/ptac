<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Grade;
use App\Models\Major;
use App\Models\School;
use Illuminate\Contracts\View\View;

class MajorIndexComposer {
    
    use ControllerTrait;

    public function compose(View $view) {
        
        $view->with(['uris' => $this->uris()]);
    }
}
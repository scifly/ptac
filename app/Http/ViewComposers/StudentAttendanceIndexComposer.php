<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class StudentAttendanceIndexComposer {

    use ModelTrait;

    public function compose(View $view) {
    
        $view->with([
            'uris' => $this->uris(),
        ]);

    }

}
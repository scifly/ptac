<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class StudentEditComposer {

    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'uris' => $this->uris()
        ]);

    }

}
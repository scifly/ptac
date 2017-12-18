<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ScoreRangeIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {
        $view->with(['uris' => $this->uris()]);
    }
}
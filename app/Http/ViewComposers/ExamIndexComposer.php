<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\ExamType;
use App\Models\School;
use App\Models\Squad;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ExamIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {
        $view->with(['uris' => $this->uris()]);
    }

}
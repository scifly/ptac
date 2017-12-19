<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ScoreIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);
    }

}
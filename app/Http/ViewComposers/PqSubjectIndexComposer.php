<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\PollQuestionnaire;
use Illuminate\Contracts\View\View;

class PqSubjectIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);
    }

}
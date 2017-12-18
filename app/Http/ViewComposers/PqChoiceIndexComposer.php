<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\PollQuestionnaireSubject;
use Illuminate\Contracts\View\View;

class PqChoiceIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);
    }

}
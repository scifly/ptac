<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\PollQuestionnaireSubject;
use Illuminate\Contracts\View\View;

class PqChoiceComposer {
    use ControllerTrait;
    protected $pqs;

    public function __construct(PollQuestionnaireSubject $pqs) {

        $this->pqs = $pqs;

    }

    public function compose(View $view) {

        $view->with([
            'pqs' => $this->pqs->pluck('subject', 'id'),
            'uris' => $this->uris()

        ]);
    }

}
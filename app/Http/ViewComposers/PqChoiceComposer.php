<?php
namespace App\Http\ViewComposers;

use App\Models\PollQuestionnaireSubject;
use Illuminate\Contracts\View\View;

class PqChoiceComposer {

    protected $pqs;

    public function __construct(PollQuestionnaireSubject $pqs) {

        $this->pqs = $pqs;

    }

    public function compose(View $view) {

        $view->with([
            'pqs' => $this->pqs->pluck('subject', 'id'),
        ]);
    }

}
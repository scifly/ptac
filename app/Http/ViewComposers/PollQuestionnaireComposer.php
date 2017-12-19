<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class PollQuestionnaireComposer {
    use ControllerTrait;
    protected $school;

    public function __construct(School $school) {

        $this->school = $school;

    }

    public function compose(View $view) {

        $view->with([
            'schools' => $this->school->pluck('name', 'id'),
            'uris' => $this->uris()

        ]);
    }

}
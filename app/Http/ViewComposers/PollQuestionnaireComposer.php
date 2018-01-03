<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class PollQuestionnaireComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'schools' => School::pluck('name', 'id'),
            'uris' => $this->uris()
        ]);
    }

}
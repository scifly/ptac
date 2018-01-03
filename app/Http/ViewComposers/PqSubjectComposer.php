<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\PollQuestionnaire;
use Illuminate\Contracts\View\View;

class PqSubjectComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'pq' => PollQuestionnaire::pluck('name', 'id'),
            'subject_type' => [0 => '单选', 1 => '多选', 2 => '填空'],
            'uris' => $this->uris()
        ]);
    }

}
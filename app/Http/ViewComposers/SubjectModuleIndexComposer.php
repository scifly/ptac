<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class SubjectModuleIndexComposer {

    use ModelTrait;

    public function compose(View $view) {
        $view->with(['uris' => $this->uris()]);
    }

}
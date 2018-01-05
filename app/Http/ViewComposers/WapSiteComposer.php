<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class WapSiteComposer {

    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'schoolId' => School::schoolId(),
            'uris' => $this->uris()
        ]);

    }

}
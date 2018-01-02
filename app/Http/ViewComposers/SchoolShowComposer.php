<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Corp;
use App\Models\SchoolType;
use Illuminate\Contracts\View\View;

class SchoolShowComposer {
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);
    }

}
<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\IconType;
use Illuminate\Contracts\View\View;

class IconIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);

    }

}
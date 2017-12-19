<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Procedure;
use Illuminate\Contracts\View\View;

class   ProcedureStepIndexComposer {
    use ControllerTrait;

    public function compose(View $view) {

        $view->with(['uris' => $this->uris()]);
    }

}
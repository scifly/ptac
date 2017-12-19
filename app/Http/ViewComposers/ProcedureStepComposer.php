<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Procedure;
use Illuminate\Contracts\View\View;

class ProcedureStepComposer {
    use ControllerTrait;
    protected $procedures;

    public function __construct(Procedure $procedures) {
        $this->procedures = $procedures;
    }

    public function compose(View $view) {

        $view->with([
            'procedures' => $this->procedures->pluck('name', 'id'),
            'uris' => $this->uris()

        ]);
    }

}
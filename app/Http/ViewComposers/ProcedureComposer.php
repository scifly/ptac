<?php

namespace App\Http\ViewComposers;

use App\Models\ProcedureType;
use App\Models\School;
use Illuminate\Contracts\View\View;

class ProcedureComposer {

    protected $schools;
    protected $procedureTypes;

    public function __construct(School $schools, ProcedureType $procedureTypes) {

        $this->schools = $schools;
        $this->procedureTypes = $procedureTypes;
    }

    public function compose(View $view) {

        $view->with([
            'schools' => $this->schools->pluck('name', 'id'),
            'procedureTypes' => $this->procedureTypes->pluck('name', 'id')
        ]);
    }

}
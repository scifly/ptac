<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\ProcedureType;
use App\Models\School;
use Illuminate\Contracts\View\View;

class ProcedureComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'schools' => School::pluck('name', 'id'),
            'procedureTypes' => ProcedureType::pluck('name', 'id'),
            'uris' => $this->uris()
        ]);
    }

}
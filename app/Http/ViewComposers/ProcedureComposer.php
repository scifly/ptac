<?php
namespace App\Http\ViewComposers;

use App\Models\ProcedureType;
use Illuminate\Contracts\View\View;

class ProcedureComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'procedureTypes' => ProcedureType::pluck('name', 'id'),
        ]);
    }
    
}
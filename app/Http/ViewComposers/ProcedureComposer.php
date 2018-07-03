<?php
namespace App\Http\ViewComposers;

use App\Models\ProcedureType;
use Illuminate\Contracts\View\View;

/**
 * Class ProcedureComposer
 * @package App\Http\ViewComposers
 */
class ProcedureComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'procedureTypes' => ProcedureType::pluck('name', 'id'),
        ]);
    }
    
}
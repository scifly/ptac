<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Procedure;
use Illuminate\Contracts\View\View;

class ProcedureStepComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'procedures' => Procedure::whereSchoolId($this->schoolId())
                ->get()->pluck('name', 'id'),
        ]);
        
    }
    
}
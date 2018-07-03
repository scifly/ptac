<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Procedure;
use Illuminate\Contracts\View\View;

/**
 * Class ProcedureStepComposer
 * @package App\Http\ViewComposers
 */
class ProcedureStepComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'procedures' => Procedure::whereSchoolId($this->schoolId())->pluck('name', 'id'),
        ]);
        
    }
    
}
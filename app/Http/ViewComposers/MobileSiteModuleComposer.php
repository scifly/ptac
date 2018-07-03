<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

/**
 * Class MobileSiteModuleComposer
 * @package App\Http\ViewComposers
 */
class MobileSiteModuleComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $school = School::find(session('schoolId'));
        $view->with([
            'acronym' => $school->corp->acronym,
        ]);
        
    }
    
}
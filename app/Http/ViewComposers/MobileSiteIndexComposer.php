<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use Illuminate\Contracts\View\View;

/**
 * Class MobileSiteIndexComposer
 * @package App\Http\ViewComposers
 */
class MobileSiteIndexComposer {
    
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
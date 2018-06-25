<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use Illuminate\Contracts\View\View;

class MobileSiteIndexComposer {
    
    public function compose(View $view) {
        
        $school = School::find(session('schoolId'));
        $view->with([
            'acronym' => $school->corp->acronym,
        ]);
        
    }
    
}
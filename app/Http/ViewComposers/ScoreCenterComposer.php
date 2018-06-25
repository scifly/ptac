<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\School;
use Illuminate\Contracts\View\View;

class ScoreCenterComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'acronym' => School::find(session('schoolId'))->corp->acronym,
        ]);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Models\ActionType;
use Illuminate\Contracts\View\View;

class ActionComposer {
    
    protected $school;
    
    public function __construct(ActionType $school) {
        
        $this->school = $school;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'schools' => $this->school->pluck('name', 'id')
        ]);
        
    }
    
}
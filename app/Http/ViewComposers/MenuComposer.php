<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\School;
use Illuminate\Contracts\View\View;

class MenuComposer {
    
    protected $school, $action;
    
    public function __construct(School $school, Action $action) {
        
        $this->school = $school;
        $this->action = $action;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'schools' => $this->school->pluck('name', 'id'),
            'actions' => $this->action->pluck('name', 'id')
        ]);
        
    }
    
}
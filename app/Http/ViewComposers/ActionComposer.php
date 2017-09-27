<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use Illuminate\Contracts\View\View;

class ActionComposer {
    
    protected $actionType;
    
    public function __construct(School $actionType) {
        
        $this->actionType = $actionType;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'actionTypes' => $this->actionType->pluck('name', 'id'),
        ]);
        
    }
    
}
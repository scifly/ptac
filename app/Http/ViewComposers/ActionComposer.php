<?php
namespace App\Http\ViewComposers;

use App\Models\ActionType;
use Illuminate\Contracts\View\View;

class ActionComposer {
    
    protected $actionType;
    
    public function __construct(ActionType $actionType) {
        
        $this->actionType = $actionType;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'actionTypes' => $this->actionType->pluck('name', 'id')
        ]);
        
    }
    
}
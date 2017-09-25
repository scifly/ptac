<?php

namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\Icon;
use Illuminate\Contracts\View\View;

class TabComposer {
    
    protected $icon, $action;
    
    public function __construct(Icon $icon, Action $action) {
        
        $this->icon = $icon;
        $this->action = $action;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'icons' => $this->icon->icons(),
            'actions' => $this->action->actions()
        ]);
        
    }
    
}
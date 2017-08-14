<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\Icon;
use App\Models\School;
use App\Models\Tab;
use Illuminate\Contracts\View\View;

class MenuComposer {
    
    protected $school, $action, $tab, $icon;
    
    public function __construct(School $school, Action $action, Tab $tab, Icon $icon) {
        
        $this->school = $school;
        $this->action = $action;
        $this->tab = $tab;
        $this->icon = $icon;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'schools' => $this->school->pluck('name', 'id'),
            'actions' => $this->action->actions(),
            'tabs' => $this->tab->pluck('name', 'id'),
            'icons' => $this->icon->icons()
        ]);
        
    }
    
}
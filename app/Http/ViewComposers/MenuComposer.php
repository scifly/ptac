<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\Icon;
use App\Models\Tab;
use Illuminate\Contracts\View\View;

class MenuComposer {
    
    protected $action, $tab, $icon;
    
    public function __construct(Action $action, Tab $tab, Icon $icon) {
        
        $this->tab = $tab;
        $this->icon = $icon;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'tabs'  => $this->tab->pluck('name', 'id'),
            'icons' => $this->icon->icons(),
        ]);
        
    }
    
}
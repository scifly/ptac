<?php
namespace App\Http\ViewComposers;

use App\Models\Action;
use App\Models\Icon;
use App\Models\School;
use App\Models\Tab;
use Illuminate\Contracts\View\View;

class TabComposer {
    
    protected $icon;
    
    public function __construct(Icon $icon) {
        
        $this->icon = $icon;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'icons' => $this->icon->icons()
        ]);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Models\IconType;
use Illuminate\Contracts\View\View;

class IconComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'iconTypes' => IconType::pluck('name', 'id'),
        ]);
        
    }
    
}
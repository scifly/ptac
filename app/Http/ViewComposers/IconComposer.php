<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\IconType;
use Illuminate\Contracts\View\View;

class IconComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'iconTypes' => IconType::pluck('name', 'id'),
            'uris'      => $this->uris(),
        ]);
        
    }
    
}
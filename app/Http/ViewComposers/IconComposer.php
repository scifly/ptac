<?php
namespace App\Http\ViewComposers;

use App\Models\IconType;
use Illuminate\Contracts\View\View;

class IconComposer {
    
    protected $iconType;
    
    public function __construct(IconType $iconType) {
        
        $this->iconType = $iconType;
        
    }
    
    public function compose(View $view) {
        
        $view->with([
            'iconTypes' => $this->iconType->pluck('name', 'id')
        ]);
        
    }
    
}
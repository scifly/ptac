<?php
namespace App\Http\ViewComposers;

use App\Models\IconType;
use Illuminate\Contracts\View\View;

/**
 * Class IconComposer
 * @package App\Http\ViewComposers
 */
class IconComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'iconTypes' => IconType::pluck('name', 'id'),
        ]);
        
    }
    
}
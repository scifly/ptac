<?php
namespace App\Http\ViewComposers;

use App\Models\Column;
use Illuminate\Contracts\View\View;

/**
 * Class ColumnComposer
 * @package App\Http\ViewComposers
 */
class ColumnComposer {
    
    /** @param View $view */
    public function compose(View $view) {
        
        $view->with(
            (new Column)->compose()
        );
        
    }
    
}
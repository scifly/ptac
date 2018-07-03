<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

/**
 * Class SchoolShowComposer
 * @package App\Http\ViewComposers
 */
class SchoolShowComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
    }
    
}
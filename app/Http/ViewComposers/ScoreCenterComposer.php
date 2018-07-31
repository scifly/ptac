<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use Illuminate\Contracts\View\View;

/**
 * Class ScoreCenterComposer
 * @package App\Http\ViewComposers
 */
class ScoreCenterComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'acronym' => session('acronym')
        ]);
        
    }
    
}
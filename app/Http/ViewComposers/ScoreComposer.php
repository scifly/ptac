<?php
namespace App\Http\ViewComposers;

use App\Models\Score;
use Exception;
use Illuminate\Contracts\View\View;

/**
 * Class ScoreComposer
 * @package App\Http\ViewComposers
 */
class ScoreComposer {
    
    /**
     * @param View $view
     * @throws Exception
     */
    public function compose(View $view) {
        
        $view->with(
            (new Score)->compose()
        );
        
    }
    
}
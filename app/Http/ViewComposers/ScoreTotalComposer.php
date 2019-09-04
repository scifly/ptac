<?php
namespace App\Http\ViewComposers;

use App\Models\ScoreTotal;
use Illuminate\Contracts\View\View;

/**
 * Class ScoreIndexComposer
 * @package App\Http\ViewComposers
 */
class ScoreTotalComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            (new ScoreTotal)->compose()
        );
        
    }
}
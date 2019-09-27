<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\ScoreRange;
use Illuminate\Contracts\View\View;

/**
 * Class ScoreRangeComposer
 * @package App\Http\ViewComposers
 */
class ScoreRangeComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with(
            (new ScoreRange)->compose()
        );
        
    }
    
}
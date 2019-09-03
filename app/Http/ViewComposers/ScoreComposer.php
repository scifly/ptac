<?php
namespace App\Http\ViewComposers;

use App\Models\Score;
use Illuminate\Contracts\View\View;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

/**
 * Class ScoreComposer
 * @package App\Http\ViewComposers
 */
class ScoreComposer {
    
    /**
     * @param View $view
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function compose(View $view) {
    
        $view->with(
            (new Score)->compose()
        );
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Models\PollQuestionnaireSubject;
use Illuminate\Contracts\View\View;

/**
 * Class PqChoiceComposer
 * @package App\Http\ViewComposers
 */
class PqChoiceComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'pqs' => PollQuestionnaireSubject::pluck('subject', 'id'),
        ]);
        
    }
    
}
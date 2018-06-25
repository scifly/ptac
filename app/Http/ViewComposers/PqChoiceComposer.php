<?php
namespace App\Http\ViewComposers;

use App\Models\PollQuestionnaireSubject;
use Illuminate\Contracts\View\View;

class PqChoiceComposer {
    
    public function compose(View $view) {
        
        $view->with([
            'pqs' => PollQuestionnaireSubject::pluck('subject', 'id'),
        ]);
        
    }
    
}
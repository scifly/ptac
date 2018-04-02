<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\PollQuestionnaireSubject;
use Illuminate\Contracts\View\View;

class PqChoiceComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $view->with([
            'pqs'  => PollQuestionnaireSubject::pluck('subject', 'id'),
            'uris' => $this->uris(),
        ]);
        
    }
    
}
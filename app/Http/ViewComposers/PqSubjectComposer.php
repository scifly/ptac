<?php
namespace App\Http\ViewComposers;

use App\Models\PollQuestionnaire;
use Illuminate\Contracts\View\View;

/**
 * Class PqSubjectComposer
 * @package App\Http\ViewComposers
 */
class PqSubjectComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'pq'           => PollQuestionnaire::pluck('name', 'id'),
            'subject_type' => [0 => '单选', 1 => '多选', 2 => '填空'],
        ]);
    }
    
}
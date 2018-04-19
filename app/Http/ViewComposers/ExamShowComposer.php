<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class ExamShowComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $exam = Exam::find(Request::route('id'));
        $view->with([
            'classes'  => $exam->selectedClasses($exam->class_ids),
            'subjects' => $exam->selectedSubjects($exam->subject_ids),
            'uris'     => $this->uris(),
        ]);
        
    }
    
}
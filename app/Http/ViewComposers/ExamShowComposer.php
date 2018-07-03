<?php
namespace App\Http\ViewComposers;

use App\Models\Exam;
use App\Models\Squad;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ExamShowComposer
 * @package App\Http\ViewComposers
 */
class ExamShowComposer {
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $exam = Exam::find(Request::route('id'));
        $classIds = explode(',', $exam->class_ids);
        $subjectIds = explode(',', $exam->subject_ids);
        $view->with([
            'classes'  => Squad::whereIn('id', $classIds)->pluck('name', 'id')->toArray(),
            'subjects' => Subject::whereIn('id', $subjectIds)->pluck('name', 'id')->toArray(),
        ]);
        
    }
    
}
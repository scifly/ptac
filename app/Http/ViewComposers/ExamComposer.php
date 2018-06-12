<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Squad;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class ExamComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $examtypes = ExamType::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $squads = Squad::whereIn('id', $this->classIds())
            ->where('enabled', 1)->pluck('name', 'id');
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        
        $selectedClasses = $selectedSubjects = [];
        if (Request::route('id')) {
            $exam = Exam::find(Request::route('id'));
            $selectedClasses = $exam->selectedClasses($exam->class_ids);
            $selectedSubjects = $exam->selectedSubjects($exam->subject_ids);
        }
        $view->with([
            'examtypes' => $examtypes,
            'classes'   => $squads,
            'subjects'  => $subjects,
            'selectedClasses' => $selectedClasses,
            'selectedSubjects' => $selectedSubjects,
            'uris'      => $this->uris(),
        ]);
        
    }
    
}
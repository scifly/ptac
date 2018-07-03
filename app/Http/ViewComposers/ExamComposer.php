<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Squad;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ExamComposer
 * @package App\Http\ViewComposers
 */
class ExamComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $gradeIds = [];
        $examtypes = ExamType::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $squads = Squad::whereIn('id', $this->classIds())
            ->where('enabled', 1)->get();
        foreach ($squads as $squad) {
            $gradeIds[] = $squad->grade_id;
        }
        $gradeIds = array_unique($gradeIds);
        $subjects = Subject::whereSchoolId($schoolId)->where('enabled', 1)->get();
        $subjectList = [];
        foreach ($subjects as $subject) {
            $intersect = array_intersect($gradeIds, explode(',', $subject->grade_ids));
            if (!empty($intersect)) {
                $subjectList[$subject->id] = $subject->name;
            }
        }
        $selectedClasses = $selectedSubjects = null;
        if (Request::route('id')) {
            $exam = Exam::find(Request::route('id'));
            $selectedClasses = Squad::whereRaw('id IN (' . $exam->class_ids . ')')->pluck('name', 'id');
            $selectedSubjects = Subject::whereRaw('id IN (' . $exam->subject_ids . ')')->pluck('name', 'id');
        }
        $view->with([
            'examtypes'        => $examtypes,
            'classes'          => $squads->pluck('name', 'id'),
            'subjects'         => $subjectList,
            'selectedClasses'  => $selectedClasses ? $selectedClasses->toArray() : [],
            'selectedSubjects' => $selectedSubjects ? $selectedSubjects->toArray() : [],
        ]);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Models\Grade;
use App\Models\Major;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class SubjectComposer
 * @package App\Http\ViewComposers
 */
class SubjectComposer {
    
    protected $major, $grade;
    
    /**
     * SubjectComposer constructor.
     * @param Major $major
     * @param Grade $grade
     */
    function __construct(Major $major, Grade $grade) {
        
        $this->major = $major;
        $this->grade = $grade;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $selectedGrades = $selectedMajors = [];
        if (Request::route('id')) {
            $subject = Subject::find(Request::route('id'));
            $selectedMajors = $subject->majors->pluck('name', 'id')->toArray();
            $gradeIds = explode(',', $subject->grade_ids);
            $selectedGrades = empty($gradeIds) ? [] : Grade::whereIn('id', $gradeIds)->pluck('name', 'id')->toArray();
        }
        $view->with([
            'grades'         => $this->grade->gradeList(),
            'majors'         => $this->major->majorList(),
            'selectedGrades' => $selectedGrades,
            'selectedMajors' => $selectedMajors,
        ]);
        
    }
    
}
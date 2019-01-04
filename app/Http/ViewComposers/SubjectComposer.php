<?php
namespace App\Http\ViewComposers;

use App\Models\Major;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class SubjectComposer
 * @package App\Http\ViewComposers
 */
class SubjectComposer {
    
    protected $subject;
    
    /**
     * SubjectComposer constructor.
     * @param Subject $subject
     */
    function __construct(Subject $subject) {
        
        $this->subject = $subject;
        
    }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with(
            array_combine(
                ['grades', 'majors', 'selectedGrades', 'selectedMajors'],
                $this->subject->compose()
            )
        );
        
    }
    
}
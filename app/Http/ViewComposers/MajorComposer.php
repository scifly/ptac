<?php
namespace App\Http\ViewComposers;

use App\Models\ActionType;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class MajorComposer {
    
    protected $school, $subject;
    
    public function __construct(School $school, Subject $subject) {
        
        $this->school = $school;
        $this->subject = $subject;
    }
    
    public function compose(View $view) {
        
        $view->with([
            'schools' => $this->school->pluck('name', 'id'),
            'subjects' =>$this->subject->pluck('name','id'),
        ]);
        
    }
    
}
<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ScoreComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'students' => Student::pluck('student_number', 'id'),
            'subjects' => Subject::pluck('name', 'id'),
            'exams' => Exam::pluck('name', 'id'),
            'uris' => $this->uris()
        ]);
        
    }

}
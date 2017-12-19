<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Exam;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ScoreComposer {
    use ControllerTrait;
    protected $students;
    protected $subjects;
    protected $exams;

    public function __construct(Student $student, Subject $subject, Exam $exam) {
        $this->students = $student;
        $this->subjects = $subject;
        $this->exams = $exam;
    }

    public function compose(View $view) {

        $view->with([
            'students' => $this->students->pluck('student_number', 'id'),
            'subjects' => $this->subjects->pluck('name', 'id'),
            'exams' => $this->exams->pluck('name', 'id'),
            'uris' => $this->uris()

        ]);
    }

}
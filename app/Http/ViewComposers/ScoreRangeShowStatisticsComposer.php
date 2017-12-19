<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class ScoreRangeShowStatisticsComposer {
    use ControllerTrait;
    protected $grade, $exam, $class;

    public function __construct(Grade $grade, Squad $class, Exam $exam) {

        $this->grade = $grade;
        $this->class = $class;
        $this->exam = $exam;

    }

    public function compose(View $view) {

        $view->with([
            'grades' => $this->grade->pluck('name', 'id'),
            'classes' => $this->class->pluck('name', 'id'),
            'exams' => $this->exam->pluck('name', 'id'),
            'uris' => $this->uris()

        ]);
    }

}
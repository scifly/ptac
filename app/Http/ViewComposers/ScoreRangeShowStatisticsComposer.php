<?php
namespace App\Http\ViewComposers;

use App\Models\Exam;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class ScoreRangeShowStatisticsComposer {

    protected $grades;

    protected $exams;

    public function __construct(Grade $grades, Squad $classes, Exam $exams) {

        $this->grades = $grades;
        $this->classes = $classes;
        $this->exams = $exams;

    }

    public function compose(View $view) {

        $view->with([
            'grades' => $this->grades->pluck('name', 'id'),
            'classes' => $this->classes->pluck('name', 'id'),
            'exams' => $this->exams->pluck('name', 'id'),
        ]);
    }

}
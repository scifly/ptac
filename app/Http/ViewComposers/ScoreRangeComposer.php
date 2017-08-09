<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use App\Models\Subject;
use App\Models\Grade;
use Illuminate\Contracts\View\View;

class ScoreRangeComposer {

    protected $schools;

    protected $subjects;

    public function __construct(School $schools,Subject $subjects,Grade $grades) {

        $this->schools = $schools;

        $this->subjects = $subjects;

        $this->grades = $grades;

    }

    public function compose(View $view) {

        // $view->with('schoolTypes', $this->schoolTypes->pluck('name', 'id'));
        $view->with([
            'schools' => $this->schools->pluck('name', 'id'),
            'subjects' => $this->subjects->pluck('name', 'id'),
            'grades' => $this->grades->pluck('name', 'id'),
        ]);
    }

}
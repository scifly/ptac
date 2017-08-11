<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ScoreRangeComposer {

    protected $schools;

    protected $subjects;

    public function __construct(School $schools,Subject $subjects) {

        $this->schools = $schools;

        $this->subjects = $subjects;

    }

    public function compose(View $view) {

        // $view->with('schoolTypes', $this->schoolTypes->pluck('name', 'id'));
        $view->with([
            'schools' => $this->schools->pluck('name', 'id'),
            'subjects' => $this->subjects->pluck('name', 'id'),
        ]);
    }

}
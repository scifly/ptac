<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use App\Models\Grade;
use Illuminate\Contracts\View\View;

class SubjectComposer {

    protected $school;

    protected $grades;

    public function __construct(School $school,Grade $grades) {

        $this->school = $school;

        $this->grades = $grades;

    }

    public function compose(View $view) {

        // $view->with('schoolTypes', $this->schoolTypes->pluck('name', 'id'));
        $view->with([
            'school' => $this->school->pluck('name', 'id'),

        ]);


        $view->with([
            'grades' => $this->grades->pluck('name', 'id'),

        ]);
    }

}
<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use App\Models\Grade;
use Illuminate\Contracts\View\View;

class SubjectComposer {

    protected $school;
    protected $grade;

    public function __construct(School $school, Grade $grade) {

        $this->school = $school;
        $this->grade = $grade;

    }

    public function compose(View $view) {

        $view->with([
            'schools' => $this->school->pluck('name', 'id'),
            'grades' => $this->grade->pluck('name', 'id'),
        ]);

    }

}
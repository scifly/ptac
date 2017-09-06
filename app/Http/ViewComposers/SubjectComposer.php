<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use App\Models\Grade;
use App\Models\Major;
use Illuminate\Contracts\View\View;

class SubjectComposer {

    protected $school,$grade, $major;

    public function __construct(School $school, Grade $grade, Major $major) {

        $this->school = $school;
        $this->grade = $grade;
        $this->major = $major;

    }

    public function compose(View $view) {

        $view->with([
            'schools' => $this->school->pluck('name', 'id'),
            'grades' => $this->grade->pluck('name', 'id'),
            'majors' => $this->major->pluck('name','id'),
        ]);

    }

}
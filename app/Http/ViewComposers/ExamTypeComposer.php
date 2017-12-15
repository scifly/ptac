<?php
namespace App\Http\ViewComposers;

use App\Models\Educator;
use App\Models\School;
use Illuminate\Contracts\View\View;

class ExamTypeComposer {

    protected $school;

    public function __construct(School $school, Educator $educator) {

        $this->school = $school;

    }

    public function compose(View $view) {
        $schoolId = $this->school->getSchoolId();
        $view->with(['schoolId' => $schoolId]);
    }

}
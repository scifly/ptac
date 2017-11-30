<?php
namespace App\Http\ViewComposers;

use App\Models\Grade;
use App\Models\Major;
use App\Models\School;
use Illuminate\Contracts\View\View;

class SubjectComposer {

    protected $school;

    public function __construct(School $school) {
        $this->school = $school;
    }

    public function compose(View $view) {
        $schoolId = $this->school->getSchoolId();
        $view->with([
            'schoolId' => $schoolId,
            'grades' => Grade::whereSchoolId($schoolId)->pluck('name', 'id'),
            'majors' => Major::whereSchoolId($schoolId)->pluck('name', 'id')
        ]);
    }
}
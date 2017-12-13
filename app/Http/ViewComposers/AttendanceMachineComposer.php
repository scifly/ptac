<?php
namespace App\Http\ViewComposers;

use App\Models\School;
use Illuminate\Contracts\View\View;

class AttendanceMachineComposer {

    protected $school;

    public function __construct(School $school) {

        $this->school = $school;

    }

    public function compose(View $view) {

        $view->with(['schoolId' => $this->school->getSchoolId()]);
    }

}
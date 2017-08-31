<?php
namespace App\Http\ViewComposers;

use App\Models\EducatorAttendanceSetting;
use App\Models\School;
use Illuminate\Contracts\View\View;

class EducatorAttendanceSettingComposer {

    protected $educatorAttendanceSetting;
    protected $school;

    public function __construct(EducatorAttendanceSetting $educatorAttendanceSetting , School $school) {

        $this->educatorAttendanceSetting = $educatorAttendanceSetting;
        $this->school = $school;

    }

    public function compose(View $view) {


        $view->with([
            'schools' => $this->school->pluck('name', 'id'),
        ]);

    }

}
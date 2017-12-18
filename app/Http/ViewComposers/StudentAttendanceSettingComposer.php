<?php

namespace App\Http\ViewComposers;

use App\Helpers\ControllerTrait;
use App\Models\Grade;
use App\Models\School;
use App\Models\Semester;
use Illuminate\Contracts\View\View;

class StudentAttendanceSettingComposer {
    use ControllerTrait;
    protected $grade, $semester, $school;

    public function __construct(Grade $grade, Semester $semester, School $school) {

        $this->grade = $grade;
        $this->semester = $semester;
        $this->school = $school;

    }

    public function compose(View $view) {

        $days = [
            '星期一' => '星期一',
            '星期二' => '星期二',
            '星期三' => '星期三',
            '星期四' => '星期四',
            '星期五' => '星期五',
            '星期六' => '星期六',
            '星期天' => '星期天',
        ];
        $schoolId = $this->school->getSchoolId();

        $view->with([
            'schoolId' => $schoolId,
            'grades' => $this->grade->pluck('name', 'id'),
            'semesters' => $this->semester->where('school_id', $schoolId)->pluck('name', 'id'),
            'days' => $days,
            'uris' => $this->uris()

        ]);

    }

}
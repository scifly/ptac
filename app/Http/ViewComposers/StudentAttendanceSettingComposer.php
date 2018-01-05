<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\School;
use App\Models\Semester;
use Illuminate\Contracts\View\View;

class StudentAttendanceSettingComposer {

    use ModelTrait;

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
        $schoolId = School::schoolId();

        $view->with([
            'schoolId' => $schoolId,
            'grades' => Grade::pluck('name', 'id'),
            'semesters' => Semester::whereSchoolId($schoolId)->pluck('name', 'id'),
            'days' => $days,
            'uris' => $this->uris()
        ]);

    }

}
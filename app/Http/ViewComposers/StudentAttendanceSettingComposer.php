<?php
namespace App\Http\ViewComposers;

use App\Models\Grade;
use App\Models\Semester;
use APP\Models\School;
use Illuminate\Contracts\View\View;

class StudentAttendanceSettingComposer {

    protected $grade,$semester,$school;

    public function __construct(Grade $grade,Semester $semester, School $school) {

        $this->grade = $grade;
        $this->semester = $semester;
        $this->school = $school;

    }

    public function compose(View $view) {
        $day = [
            '星期一' => '星期一',
            '星期二' => '星期二',
            '星期三' => '星期三',
            '星期四' => '星期四',
            '星期五' => '星期五',
            '星期六' => '星期六',
            '星期天' => '星期天'
        ];
        dd($this->school->pluck('name', 'id'));
        $view->with([
            'schools' => $this->school->pluck('name', 'id'),
            'grades' => $this->grade->pluck('name', 'id'),
            'semesters' => $this->semester->pluck('name','id'),
            'days' => $day
        ]);

    }

}
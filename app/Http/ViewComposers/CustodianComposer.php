<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Group;
use App\Models\School;
use App\Models\Squad;
use App\Models\Student;
use Illuminate\Contracts\View\View;

class CustodianComposer {

    use ModelTrait;

    public function compose(View $view) {

        $schools = null;
        $grades = null;
        $classes = null;
        $students = null;

        $schoolId = School::schoolId();
        $schools = School::whereId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        if ($schools) {
            $grades = Grade::whereSchoolId($schoolId)
                ->where('enabled', 1)
                ->pluck('name', 'id');
        }
        if ($grades) {
            $classes = Squad::whereGradeId($grades->keys()->first())
                ->where('enabled', 1)
                ->pluck('name', 'id');
        }
        if ($classes) {
            $list = Student::whereClassId($classes->keys()->first())
                ->where('enabled', 1)
                ->get();
            if (!empty($list)) {
                foreach ($list as $s) {
                    $students[$s->id] = $s->user->realname . "-" . $s->student_number;
                }
            }
        }
        if (empty($students)) {$students[] = '' ;}
        if (empty($classes)) {$classes[] = '' ;}
        if (empty($grades)) {$grades[] = '' ;}
        $view->with([
            'schools' => $schools,
            'grades' => $grades,
            'classes' => $classes,
            'students' => $students,
            'groupId' => Group::whereName('监护人')->first()->id,
            'uris' => $this->uris()
        ]);

    }

}
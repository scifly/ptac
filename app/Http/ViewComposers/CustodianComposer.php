<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Group;
use App\Models\School;
use App\Models\Squad;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class CustodianComposer {

    use ModelTrait;
    
    protected $student;
    
    function __construct(Student $student) { $this->student = $student; }
    
    public function compose(View $view) {

        $schools = null;
        $grades = null;
        $classes = null;
        $students = null;

        $schoolId = $this->schoolId();
        $schools = School::whereId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $groupId = Auth::user()->group->id;
        if($groupId > 5){
            $educatorId = Auth::user()->educator->id;
            $gradeIds = $this->student->getGrade($educatorId)[0];
            $gradeClass = $this->student->getGrade($educatorId)[1];
            foreach ($gradeClass as $k=>$g){
                $grades = Grade::whereEnabled(1)
                    ->whereIn('id',$gradeIds)
                    ->pluck('name', 'id')
                    ->toArray();
                $classes = Squad::whereEnabled(1)
                    ->whereIn('id',$g)
                    ->pluck('name', 'id')
                    ->toArray();
                break;
            }
            foreach($classes as $k=>$c){
                $list = Student::whereClassId($k)
                    ->where('enabled', 1)
                    ->get();
                break;
            }


        }else{
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
            }
        }
        if (!empty($list)) {
            foreach ($list as $s) {
                $students[$s->id] = $s->user->realname . "-" . $s->student_number;
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
<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class StudentComposer {

    use ModelTrait;

    public function compose(View $view) {

        $schoolId = School::schoolId();
        $user = Auth::user();
        $role = $user->group->id;
        if($role > 5){
            $educatorId = $user->educator->id;
            $grades = Student::getGrade($educatorId)[0];
            $classes = Student::getGrade($educatorId)[1];
        }else{
            $grades = Grade::whereEnabled(1)
                ->where('school_id', $schoolId)
                ->pluck('name', 'id')
                ->toArray();
            if (empty($grades)){
                $classes = [];
            }else {
                $classes = Squad::whereEnabled(1)
                    ->where('grade_id', array_keys($grades)[0])
                    ->pluck('name', 'id')
                    ->toArray();
            }
        }
        if (empty($classes)) {$classes[] = '' ;}
        if (empty($grades)) {$grades[] = '' ;}
        $view->with([
            'grades' => $grades,
            'classes' => $classes,
            'uris' => $this->uris()
        ]);

    }

}
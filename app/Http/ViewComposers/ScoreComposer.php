<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\School;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ScoreComposer {
    
    use ModelTrait;

    public function compose(View $view) {
        $schoolId = School::schoolId();
        $school = School::whereId($schoolId)->first();
        #获取学校下所有班级 和 考试
        $squadIds = [];
        $examarr = [];
        $squads = $school->classes;
        foreach ($squads as $squad){
            $squadIds[] = $squad->id;
        }
        #获取学校下所有学生
        $students = [];
        foreach ($squads as $squad) {
            foreach ($squad->students as $stu) {
                $students[$stu->id] = $stu->student_number . '-' . $stu->user->realname;
            }
        }
        
        #显示的考试
        $examAll = Exam::whereEnabled(1)->get();
        foreach ($examAll as $item){
            #筛选出属于本校的考试
            if(empty(array_diff(explode(',', $item->class_ids), $squadIds))){
                $examarr[$item->id] = $item->name;
            }
        }
        $subjects = Subject::whereEnabled(1)
            ->whereSchoolId($schoolId)
            ->pluck('name', 'id');
        if (empty($exams)) {$exams[] = '' ;}
        if (empty($subjects)) {$subjects[] = '' ;}
        if (empty($students)) {$students[] = '' ;}
        
        $view->with([
            'subjects' => $subjects,
            'exams' => $examarr,
            'students' => $students,
            'uris' => $this->uris()
        ]);
        
    }

}
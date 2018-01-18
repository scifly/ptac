<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class ScoreIndexComposer {
    
    use ModelTrait;

    public function compose(View $view) {
        $schoolId = School::schoolId();
        $school = School::whereId($schoolId)->first();
        $grades = Grade::whereEnabled(1)
            ->where('school_id', $schoolId)
            ->pluck('name', 'id')
            ->toArray();
        #获取学校下所有班级
        $squadIds = [];
        $exams = [];
        $squads = $school->classes;
        foreach ($squads as $squad){
            $squadIds[] = $squad->id;
        }
        #显示的考试
        $examAll = Exam::whereEnabled(1)->get();
        foreach ($examAll as $item){
            #筛选出属于本校的考试
            if(empty(array_diff(explode(',', $item->class_ids), $squadIds))){
                $exams[$item->id] = $item->name;
            }
        }
        #默认显示的班级
        $classes = Squad::whereEnabled(1)
            ->where('grade_id', array_keys($grades)[0])
            ->pluck('name', 'id')
            ->toArray();
            
        if (empty($classes)) {$classes[] = '' ;}
        if (empty($grades)) {$grades[] = '' ;}
        if (empty($exams)) {$exams[] = '' ;}

        $view->with([
            'uris' => $this->uris(),
            'grades' => $grades,
            'classes' => $classes,
            'exams' => $exams,
            ]);
        
    }

}
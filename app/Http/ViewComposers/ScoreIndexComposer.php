<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\School;
use App\Models\Squad;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ScoreIndexComposer {
    
    use ModelTrait;

    public function compose(View $view) {
        $exams = Exam::get()->pluck('name', 'id')->toArray();
        if ($exams) {
            $ids = Exam::whereId(array_keys($exams)[0])->first();
        
            $classes = Squad::whereIn('id', explode(',', $ids['class_ids']))
                ->pluck('name', 'id')
                ->toArray();
            $subjects = Subject::whereIn('id', explode(',', $ids['subject_ids']))
                ->get()
                ->toArray();
        }
        
        $schoolId = School::schoolId();
        $school = School::whereId($schoolId)->first();
        #获取学校下所有班级 和 考试
        $squadIds = [];
        $examarr = [];
        $squads = $school->classes;
        foreach ($squads as $squad){
            $squadIds[] = $squad->id;
        }
        #显示的考试
        $examAll = Exam::whereEnabled(1)->get();
        foreach ($examAll as $item){
            #筛选出属于本校的考试
            if(empty(array_diff(explode(',', $item->class_ids), $squadIds))){
                $examarr[$item->id] = $item->name;
            }
        }
        
        if (empty($exams)) {$exams[] = '' ;}
        if (empty($examarr)) {$examarr[] = '' ;}
        if (empty($classes)) {$classes[] = '' ;}
        if (empty($subjects)) {$subjects[] = '' ;}
    
        $view->with([
            'uris' => $this->uris(),
            'classes' => $classes,
            'exams' => $exams,
            'examarr' => $examarr,
            'subjects' => $subjects,
            ]);
        
    }
}
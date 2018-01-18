<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
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


        if (empty($exams)) {$exams[] = '' ;}
        if (empty($classes)) {$classes[] = '' ;}
        if (empty($subjects)) {$subjects[] = '' ;}

        $view->with([
            'uris' => $this->uris(),
            'exams' => $exams,
            'classes' => $classes,
            'subjects' => $subjects,
            ]);
        
    }

}
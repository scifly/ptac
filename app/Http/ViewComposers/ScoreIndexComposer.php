<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class ScoreIndexComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $exams = Exam::get()->toArray();
        if ($exams) {
            $ids = Exam::whereId(array_keys($exams)[0])->first();

            $classes = Squad::where('id', explode(',', $ids->class_ids))
                ->pluck('name', 'id')
                ->toArray();
        }


        if (empty($exams)) {$exams[] = '' ;}
        if (empty($classes)) {$classes[] = '' ;}

        $view->with([
            'uris' => $this->uris(),
            'exams' => $exams,
            'classes' => $classes,
            ]);
        
    }

}
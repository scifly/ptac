<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class ScoreRangeStatComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $grades = Grade::whereEnabled(1)
            ->whereIn('id', $this->gradeIds())
            ->pluck('name', 'id')->toArray();
        $classes = Squad::whereEnabled(1)
            ->whereIn('id', $this->classIds())
            ->pluck('name', 'id')->toArray();
        $exams = Exam::whereEnabled(1)
            ->whereRaw('class_ids IN(' . implode(',', $this->classIds()) . ')')
            ->pluck('name', 'id');
        $view->with([
            'grades'  => $grades,
            'classes' => $classes,
            'exams'   => $exams,
        ]);
        
    }
    
}
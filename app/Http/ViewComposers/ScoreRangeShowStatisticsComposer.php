<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class ScoreRangeShowStatisticsComposer {
    
    use ModelTrait;

    public function compose(View $view) {

        $view->with([
            'grades' => Grade::pluck('name', 'id'),
            'classes' => Squad::pluck('name', 'id'),
            'exams' => Exam::pluck('name', 'id'),
            'uris' => $this->uris()
        ]);
        
    }

}
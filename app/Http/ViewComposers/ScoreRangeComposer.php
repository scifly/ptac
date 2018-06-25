<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\ScoreRange;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

class ScoreRangeComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $subjects = Subject::whereSchoolId($schoolId)
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        array_unshift($subjects, '总分');
        $selectedSubjects = null;
        if (Request::route('id')) {
            $sr = ScoreRange::find(Request::route('id'));
            $ids = explode(',', $sr->subject_ids);
            $selectedSubjects = [];
            foreach ($ids as $id) {
                if ($id == 0) {
                    $selectedSubjects[$id] = '总分';
                } else {
                    $selectedSubjects[$id] = Subject::find($id)->name;
                }
            }
        }
        $view->with([
            'subjects'         => $subjects,
            'selectedSubjects' => $selectedSubjects,
        ]);
        
    }
    
}
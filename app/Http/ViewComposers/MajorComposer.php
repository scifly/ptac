<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Major;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class MajorComposer
 * @package App\Http\ViewComposers
 */
class MajorComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $selectedSubjects = [];
        if (Request::route('id')) {
            $selectedSubjects = Major::find(Request::route('id'))
                ->subjects->pluck('name', 'id')->toArray();
        }
        $view->with([
            'subjects'         => Subject::whereSchoolId($schoolId)->pluck('name', 'id'),
            'selectedSubjects' => $selectedSubjects,
        ]);
        
    }
    
}
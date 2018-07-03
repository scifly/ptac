<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

/**
 * Class SubjectModuleComposer
 * @package App\Http\ViewComposers
 */
class SubjectModuleComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $subjects = Subject::whereSchoolId($this->schoolId())
            ->where('enabled', 1)
            ->pluck('name', 'id');
        $view->with([
            'subjects' => $subjects,
        ]);
        
    }
    
}
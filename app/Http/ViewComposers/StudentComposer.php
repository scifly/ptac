<?php

namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class StudentComposer {

    use ModelTrait;
    
    protected $student;
    
    function __construct(Student $student) { $this->student = $student; }
    
    public function compose(View $view) {
    
        $grades = Grade::whereIn('id', $this->gradeIds())
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        reset($grades);
        $classes = Squad::whereGradeId(key($grades))
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        
        $view->with([
            'grades' => $grades,
            'classes' => $classes,
            'uris' => $this->uris()
        ]);

    }

}
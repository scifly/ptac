<?php

namespace App\Http\ViewComposers;

use App\Models\Corp;
use App\Models\Educator;
use App\Models\ExamType;
use App\Models\School;
use App\Models\SchoolType;
use App\Models\Squad;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class ExamComposer {

    protected $examtypes;
    protected $classes;
    protected $subjects;

    public function __construct(ExamType $examtypes, Squad $classes, Subject $subjects) {

        $this->examtypes = $examtypes;
        $this->classes = $classes;
        $this->subjects = $subjects;

    }

    public function compose(View $view) {

        $view->with([
            'examtypes' => $this->examtypes->pluck('name', 'id'),
            'classes' => $this->classes->pluck('name', 'id'),
            'subjects' => $this->subjects->pluck('name', 'id'),
        ]);
    }

}
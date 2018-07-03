<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class StudentComposer
 * @package App\Http\ViewComposers
 */
class StudentComposer {
    
    use ModelTrait;
    
    protected $student;
    
    /**
     * StudentComposer constructor.
     * @param Student $student
     */
    function __construct(Student $student) { $this->student = $student; }
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $grades = Grade::whereIn('id', $this->gradeIds())
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        if (Request::route('id')) {
            $gradeId = Student::find(Request::route('id'))->squad->grade_id;
        } else {
            reset($grades);
            $gradeId = key($grades);
        }
        $classes = Squad::whereGradeId($gradeId)
            ->where('enabled', 1)
            ->pluck('name', 'id')
            ->toArray();
        $mobiles = $user = null;
        if (Request::route('id')) {
            $student = Student::find(Request::route('id'));
            $user = $student->user;
            $mobiles = $student->user->mobiles;
        }
        $view->with([
            'grades'  => $grades,
            'classes' => $classes,
            'user'    => $user,
            'mobiles' => $mobiles,
        ]);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Student;
use Illuminate\Contracts\View\View;

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
        
        list($grades, $classes, $user, $mobiles) = $this->student->compose();
        
        $view->with([
            'grades'  => $grades,
            'classes' => $classes,
            'user'    => $user,
            'mobiles' => $mobiles
        ]);
        
    }
    
}
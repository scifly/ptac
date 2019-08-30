<?php
namespace App\Http\ViewComposers;

use App\Models\Student;
use Illuminate\Contracts\View\View;
use ReflectionException;

/**
 * Class StudentComposer
 * @package App\Http\ViewComposers
 */
class StudentComposer {
    
    protected $student;
    
    /**
     * StudentComposer constructor.
     * @param Student $student
     */
    function __construct(Student $student) {
        
        $this->student = $student;
    
    }
    
    /**
     * @param View $view
     * @throws ReflectionException
     */
    public function compose(View $view) {
        
        $view->with(
            $this->student->compose()
        );
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use ReflectionException;

/**
 * Class ConsumptionStatComposer
 * @package App\Http\ViewComposers
 */
class ConsumptionStatComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     * @throws ReflectionException
     */
    public function compose(View $view) {
        
        $ranges = [
            1 => '学生',
            2 => '班级',
            3 => '年级',
        ];
        $students = [];
        $values = Student::whereIn('id', $this->contactIds('student'))->get();
        foreach ($values as $v) {
            $students[$v->id] = $v->user->realname . '(' . $v->squad->grade->name . ' / ' . $v->squad->name . ')';
        }
        $classes = Squad::whereIn('id', $this->classIds())->pluck('name', 'id')->toArray();
        $grades = Grade::whereIn('id', $this->gradeIds())->pluck('name', 'id')->toArray();
        $view->with([
            'ranges'   => $ranges,
            'students' => $students,
            'classes'  => $classes,
            'grades'   => $grades,
        ]);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\StudentAttendance;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class TurnstileComposer
 * @package App\Http\ViewComposers
 */
class AttendanceCustodianComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $students = Auth::user()->custodian->students;
        foreach ($students as $student) {
            $data = (new StudentAttendance)->wStat($student->id);
            $student->abnormal = count($data['aDays']);
            $student->normal = count($data['nDays']);
            $student->schoolname = $student->squad->grade->school->name;
            $student->studentname = $student->user->realname;
            $student->classname = $student->squad->name;
        }
        
        $view->with([
            'students' => $students
        ]);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Squad;
use App\Models\StudentAttendanceSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

/**
 * Class AttendanceMachineComposer
 * @package App\Http\ViewComposers
 */
class AttendanceEducatorComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $schoolId = session('schoolId');
        $classes = Squad::whereIn('id', $this->classIds($schoolId, Auth::id()))
            ->pluck('name', 'id')->toArray();
        reset($classes);
        $gradeId = Squad::find(key($classes))->grade_id;
        $sases = StudentAttendanceSetting::whereGradeId($gradeId)->pluck('name', 'id')->toArray();
        
        $view->with([
            'classes' => $classes,
            'sases' => $sases
        ]);
        
    }
    
}
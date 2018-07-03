<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Semester;
use Illuminate\Contracts\View\View;

/**
 * Class StudentAttendanceSettingComposer
 * @package App\Http\ViewComposers
 */
class StudentAttendanceSettingComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $days = [
            '星期一' => '星期一',
            '星期二' => '星期二',
            '星期三' => '星期三',
            '星期四' => '星期四',
            '星期五' => '星期五',
            '星期六' => '星期六',
            '星期天' => '星期日',
        ];
        $schoolId = $this->schoolId();
        $grades = Grade::whereEnabled(1)
            ->where('school_id', $schoolId)
            ->pluck('name', 'id')
            ->toArray();
        if (empty($grades)) {
            $grades[] = '';
        }
        $view->with([
            'grades'    => $grades,
            'semesters' => Semester::whereSchoolId($schoolId)->pluck('name', 'id'),
            'days'      => $days,
        ]);
        
    }
    
}
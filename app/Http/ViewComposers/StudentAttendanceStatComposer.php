<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

class StudentAttendanceStatComposer {
    
    use ModelTrait;
    
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
            'titles'  => ['姓名', '监护人', '手机号码', '打卡时间', '进/出'],
            'grades'  => $grades,
            'classes' => $classes,
        ]);
        
    }
    
}
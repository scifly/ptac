<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\School;
use App\Models\Student;
use Illuminate\Contracts\View\View;

/**
 * Class ScoreAnalysisComposer
 * @package App\Http\ViewComposers
 */
class ScoreAnalysisComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $schoolId = $this->schoolId();
        $school = School::whereId($schoolId)->first();
        #获取学校下所有班级 和 考试
        $squadIds = [];
        $examarr = [];
        $classes = [];
        $squads = $school->classes;
        foreach ($squads as $squad) {
            $squadIds[] = $squad->id;
            $classes[$squad->id] = $squad->name;
        }
        #显示的考试
        $examAll = Exam::whereEnabled(1)->get();
        foreach ($examAll as $item) {
            #筛选出属于本校的考试
            if (empty(array_diff(explode(',', $item->class_ids), $squadIds))) {
                $examarr[$item->id] = $item->name;
            }
        }
        $students = [];
        if (!empty($classes)) {
            $stuData = Student::whereEnabled(1)
                ->whereClassId(array_keys($classes)[0])
                ->get();
            foreach ($stuData as $stu) {
                $students[$stu->id] = $stu->sn . '-' . $stu->user->realname;
            }
        }
        !empty($examarr) ?: $examarr[] = '';
        !empty($classes) ?: $classes[] = '';
        !empty($students) ?: $students[] = '';
        
        $view->with([
            'classes'  => $classes,
            'examarr'  => $examarr,
            'students' => $students,
        ]);
        
    }
}
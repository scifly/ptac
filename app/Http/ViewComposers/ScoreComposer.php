<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ScoreComposer
 * @package App\Http\ViewComposers
 */
class ScoreComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        /** 班级、考试、科目和学生列表 */
        # 对当前用户可见的考试列表
        $examList = Exam::whereEnabled(1)
            ->whereIn('id', $this->examIds())
            ->pluck('name', 'id')->toArray();
        if (Request::route('id')) {
            $exam = Score::find(Request::route('id'))->exam;
        } else {
            reset($examList);
            $exam = Exam::find(key($examList));
        }
        # 指定考试对应的班级
        $classIds = Squad::whereEnabled(1)
            ->whereIn('id', explode(',', $exam ? $exam->class_ids : ''))
            ->pluck('id')->toArray();
        # 指定考试对应的科目列表
        $subjectList = Subject::whereEnabled(1)
            ->whereIn('id', explode(',', $exam ? $exam->subject_ids : ''))
            ->pluck('name', 'id')->toArray();
        # 指定考试对应的且对当前用户可见的学生列表
        $studentList = [];
        $students = Student::whereEnabled(1)
            ->whereIn('class_id', array_intersect($classIds, $this->classIds()))->get();
        foreach ($students as $student) {
            $studentList[$student->id] = $student->student_number . ' - ' . $student->user->realname;
        }
        $view->with([
            'subjects' => $subjectList,
            'exams'    => $examList,
            'students' => $studentList,
        ]);
        
    }
    
}
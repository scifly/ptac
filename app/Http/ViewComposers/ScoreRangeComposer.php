<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\ScoreRange;
use App\Models\Squad;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ScoreRangeComposer
 * @package App\Http\ViewComposers
 */
class ScoreRangeComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                $data = [
                    'buttons' => [
                        'stat' => [
                            'id'    => 'stat',
                            'label' => '统计',
                            'icon'  => 'fa fa-bar-chart',
                        ],
                    ],
                    'titles'  => ['#', '名称', '起始分数', '截止分数', '创建于', '更新于', '状态 . 操作'],
                ];
                break;
            case 'stat':
                $grades = Grade::whereEnabled(1)
                    ->whereIn('id', $this->gradeIds())
                    ->pluck('name', 'id')->toArray();
                $classes = Squad::whereEnabled(1)
                    ->whereIn('id', $this->classIds())
                    ->pluck('name', 'id')->toArray();
                $exams = Exam::whereEnabled(1)
                    ->whereRaw('class_ids IN(' . implode(',', $this->classIds()) . ')')
                    ->pluck('name', 'id');
                $data = [
                    'grades'  => $grades,
                    'classes' => $classes,
                    'exams'   => $exams,
                ];
                break;
            default:
                $schoolId = $this->schoolId();
                $subjects = Subject::whereSchoolId($schoolId)
                    ->where('enabled', 1)
                    ->pluck('name', 'id')
                    ->toArray();
                array_unshift($subjects, '总分');
                if (Request::route('id')) {
                    $sr = ScoreRange::find(Request::route('id'));
                    $ids = explode(',', $sr->subject_ids);
                    $selectedSubjects = [];
                    foreach ($ids as $id) {
                        if ($id == 0) {
                            $selectedSubjects[$id] = '总分';
                        } else {
                            $selectedSubjects[$id] = Subject::find($id)->name;
                        }
                    }
                }
                $data = [
                    'subjects'         => $subjects,
                    'selectedSubjects' => $selectedSubjects ?? null,
                ];
                break;
        }
        
        $view->with($data);
        
    }
    
}
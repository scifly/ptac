<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\ExamType;
use App\Models\Squad;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;

/**
 * Class ExamComposer
 * @package App\Http\ViewComposers
 */
class ExamComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                $htmlExamType = $this->singleSelectList(
                    array_merge(
                        [null => '全部'],
                        ExamType::whereSchoolId($this->schoolId())->get()->pluck('name', 'id')->toArray()
                    ),
                    'filter_exam_type'
                );
                $data = [
                    'titles' => [
                        '#', '名称',
                        ['title' => '类型', 'html' => $htmlExamType],
                        '满分', '及格分数',
                        [
                            'title' => '开始日期',
                            'html' => $this->inputDateTimeRange('开始日期', false)
                        ],
                        [
                            'title' => '结束日期',
                            'html' => $this->inputDateTimeRange('结束日期', false)
                        ],
                        [
                            'title' => '创建于',
                            'html' => $this->inputDateTimeRange('创建于')
                        ],
                        [
                            'title' => '更新于',
                            'html' => $this->inputDateTimeRange('更新于')
                        ],
                        [
                            'title' => '状态 . 操作',
                            'html' => $this->singleSelectList(
                                [null => '全部', 0 => '已禁用', 1 => '已启用'], 'filter_enabled'
                            )
                        ],
                    ],
                    'batch' => true,
                    'filter' => true,
                ];
                break;
            case 'show':
                $exam = Exam::find(Request::route('id'));
                $classIds = explode(',', $exam->class_ids);
                $subjectIds = explode(',', $exam->subject_ids);
                $data = [
                    'classes'  => Squad::whereIn('id', $classIds)->pluck('name', 'id')->toArray(),
                    'subjects' => Subject::whereIn('id', $subjectIds)->pluck('name', 'id')->toArray(),
                ];
                break;
            default:
                $schoolId = $this->schoolId();
                $gradeIds = [];
                $examtypes = ExamType::whereSchoolId($schoolId)
                    ->where('enabled', 1)
                    ->pluck('name', 'id');
                $squads = Squad::whereIn('id', $this->classIds())
                    ->where('enabled', 1)->get();
                foreach ($squads as $squad) {
                    $gradeIds[] = $squad->grade_id;
                }
                $gradeIds = array_unique($gradeIds);
                $subjects = Subject::whereSchoolId($schoolId)->where('enabled', 1)->get();
                $subjectList = [];
                foreach ($subjects as $subject) {
                    $intersect = array_intersect($gradeIds, explode(',', $subject->grade_ids));
                    if (!empty($intersect)) {
                        $subjectList[$subject->id] = $subject->name;
                    }
                }
                $selectedClasses = $selectedSubjects = null;
                if (Request::route('id')) {
                    $exam = Exam::find(Request::route('id'));
                    $selectedClasses = Squad::whereRaw('id IN (' . $exam->class_ids . ')')->pluck('name', 'id');
                    $selectedSubjects = Subject::whereRaw('id IN (' . $exam->subject_ids . ')')->pluck('name', 'id');
                }
                $data = [
                    'examtypes'        => $examtypes,
                    'classes'          => $squads->pluck('name', 'id'),
                    'subjects'         => $subjectList,
                    'selectedClasses'  => $selectedClasses ? $selectedClasses->toArray() : [],
                    'selectedSubjects' => $selectedSubjects ? $selectedSubjects->toArray() : [],
                ];
                break;
        }
        
        $view->with($data);
        
    }
    
}
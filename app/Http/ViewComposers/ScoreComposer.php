<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Request;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

/**
 * Class ScoreComposer
 * @package App\Http\ViewComposers
 */
class ScoreComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws Exception
     */
    public function compose(View $view) {
    
        $action = explode('/', Request::path())[1];
        switch ($action) {
            case 'index':
                $examList = Exam::whereEnabled(1)
                    ->whereIn('id', $this->examIds())
                    ->pluck('name', 'id')
                    ->toArray();
                $exam = Exam::find(array_key_first($examList));
                # 指定考试对应的班级
                $classIds = array_intersect(
                    explode(',', $exam ? $exam->class_ids : ''),
                    $this->classIds()
                );
                $classList = Squad::whereEnabled(1)
                    ->whereIn('id', $classIds)
                    ->pluck('name', 'id')->toArray();
                # 生成指定考试和班级的成绩导入模板
                if ($exam) {
                    (new Score)->template($exam->id, key($classList));
                }
                $subjectList = Subject::whereEnabled(1)
                    ->whereIn('id', explode(',', $exam ? $exam->subject_ids : ''))
                    ->pluck('name', 'id')->toArray();
                $items = [
                    'score'      => '分数',
                    'grade_rank' => '年排名',
                    'class_rank' => '班排名',
                    'grade_avg'  => '年平均',
                    'class_avg'  => '班平均',
                    'grade_max'  => '年最高',
                    'class_max'  => '班最高',
                    'grade_min'  => '年最低',
                    'class_min'  => '班最低',
                ];
                $optionAll = [null => '全部'];
                $htmlClass = $this->htmlSelect(
                    $optionAll + Squad::whereIn('id', $this->classIds())->pluck('name', 'id')->toArray(),
                    'filter_class'
                );
                $htmlGrade = $this->htmlSelect(
                    $optionAll + Grade::whereIn('id', $this->gradeIds())->pluck('name', 'id')->toArray(),
                    'filter_grade'
                );
                $htmlSubject = $this->htmlSelect(
                    $optionAll + Subject::whereSchoolId($this->schoolId())->pluck('name', 'id')->toArray(),
                    'filter_grade'
                );
                $htmlExam = $this->htmlSelect(
                    $optionAll + Exam::whereIn('id', $this->examIds())->pluck('name', 'id')->toArray(),
                    'filter_grade'
                );
                $data = [
                    'buttons'        => [
                        'send'   => [
                            'id'    => 'send',
                            'label' => '成绩发送',
                            'icon'  => 'fa fa-send-o',
                        ],
                        'import' => [
                            'id'    => 'import',
                            'label' => '批量导入',
                            'icon'  => 'fa fa-upload',
                        ],
                        'export' => [
                            'id'    => 'export',
                            'label' => '批量导出',
                            'icon'  => 'fa fa-download',
                        ],
                        'rank'   => [
                            'id'    => 'rank',
                            'label' => ' 排名',
                            'icon'  => 'fa fa-sort-numeric-asc',
                        ],
                        'stat'   => [
                            'id'    => 'stat',
                            'label' => '统计分析',
                            'icon'  => 'fa fa-bar-chart-o',
                        ],
                    ],
                    'titles'         => [
                        '#', '姓名', '学号',
                        ['title' => '年级', 'html' => $htmlGrade],
                        ['title' => '班级', 'html' => $htmlClass],
                        ['title' => '科目名称', 'html' => $htmlSubject],
                        ['title' => '考试名称', 'html' => $htmlExam],
                        '成绩', '年级排名', '班级排名',
                        [
                            'title' => '创建于',
                            'html'  => $this->htmlDTRange('创建于'),
                        ],
                        [
                            'title' => '更新于',
                            'html'  => $this->htmlDTRange('更新于'),
                        ],
                        [
                            'title' => '状态 . 操作',
                            'html'  => $this->htmlSelect(
                                [null => '全部', 0 => '已禁用', 1 => '已启用'], 'filter_enabled'
                            ),
                        ],
                    ],
                    'batch'          => true,
                    'filter'         => true,
                    'exams'          => $examList,
                    'classes'        => $classList,
                    'subjects'       => $subjectList,
                    'items'          => $items,
                    'importTemplate' => $this->filePath('scores') . '.xlsx',
                ];
                break;
            case 'stat':
                /** 班级、考试、科目和学生列表 */
                # 对当前用户可见的考试列表
                $examList = Exam::whereEnabled(1)->whereIn('id', $this->examIds())
                    ->pluck('name', 'id')->toArray();
                $exam = Exam::find(array_key_first($examList));
                # 指定考试对应的班级
                $classList = Squad::whereEnabled(1)
                    ->whereIn('id', $exam ? explode(',', $exam->class_ids) : [])
                    ->pluck('name', 'id')->toArray();
                $class = Squad::find(array_key_first($classList));
                # 指定考试对应的科目列表
                $subjectList = Subject::whereEnabled(1)
                    ->whereIn('id', $exam ? explode(',', $exam->subject_ids) : [])
                    ->pluck('name', 'id')->toArray();
                # 指定考试对应的且对当前用户可见的学生列表
                $studentList = [];
                $students = Student::whereClassId($class ? $class->id : 0)->get();
                foreach ($students as $student) {
                    $studentList[$student->id] = $student->sn . ' - ' . $student->user->realname;
                }
                $data = [
                    'subjects' => $subjectList,
                    'classes'  => $classList,
                    'exams'    => $examList,
                    'students' => $studentList,
                ];
                break;
            default:
                /** 班级、考试、科目和学生列表 */
                # 对当前用户可见的考试列表
                $examList = Exam::whereEnabled(1)
                    ->whereIn('id', $this->examIds())
                    ->pluck('name', 'id')->toArray();
                $exam = Request::route('id')
                    ? Score::find(Request::route('id'))->exam
                    : Exam::find(array_key_first($examList));
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
                    $studentList[$student->id] = $student->sn . ' - ' . $student->user->realname;
                }
                $data = [
                    'subjects' => $subjectList,
                    'exams'    => $examList,
                    'students' => $studentList,
                ];
                break;
        }
        
        $view->with($data);
        
    }
    
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Score;
use App\Models\Squad;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

/**
 * Class ScoreIndexComposer
 * @package App\Http\ViewComposers
 */
class ScoreIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function compose(View $view) {
        
        $examList = Exam::whereEnabled(1)
            ->whereIn('id', $this->examIds())
            ->pluck('name', 'id')
            ->toArray();
        reset($examList);
        $exam = Exam::find(key($examList));
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
            'score'         => '分数',
            'grade_rank'    => '年排名',
            'class_rank'    => '班排名',
            'grade_average' => '年平均',
            'class_average' => '班平均',
            'grade_max'     => '年最高',
            'class_max'     => '班最高',
            'grade_min'     => '年最低',
            'class_min'     => '班最低',
        ];
        $view->with([
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
                '#', '姓名', '年级', '班级', '学号', '科目名称',
                '考试名称', '班级排名', '年级排名', '成绩',
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
            'batch'          => true,
            'filter'         => true,
            'exams'          => $examList,
            'classes'        => $classList,
            'subjects'       => $subjectList,
            'items'          => $items,
            'importTemplate' => 'uploads/' . date('Y/m/d/') . 'scores.xlsx',
        ]);
        
    }
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\School;
use App\Models\ScoreTotal;
use App\Models\Squad;
use App\Models\Subject;
use Illuminate\Contracts\View\View;

class ScoreIndexComposer {
    
    use ModelTrait;
    
    public function compose(View $view) {
        
        $examList = Exam::whereEnabled(1)
            ->whereIn('id', $this->examIds())
            ->get()->pluck('name', 'id')
            ->toArray();
        reset($examList);
        $exam = Exam::find(key($examList));
        # 指定考试对应的班级
        $classList = Squad::whereEnabled(1)
            ->whereIn('id', array_intersect(explode(',', $exam->class_ids), $this->classIds()))
            ->get()->pluck('name', 'id')->toArray();
        $subjectList = Subject::whereEnabled(1)
            ->whereIn('id', explode(',', $exam->subject_ids))
            ->get()->pluck('name', 'id')->toArray();

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
                    'icon'  => 'fa fa-arrow-circle-up',
                ],
                'export' => [
                    'id'    => 'export',
                    'label' => '批量导出',
                    'icon'  => 'fa fa-arrow-circle-down',
                ],
                'rank'   => [
                    'id'    => 'rank',
                    'label' => ' 排名统计',
                    'icon'  => 'fa fa-sort-numeric-asc',
                ],
                'stat'   => [
                    'id'    => 'stat',
                    'label' => '统计分析',
                    'icon'  => 'fa fa-bar-chart-o',
                ],
            ],
            'titles'         => [
                '#', '姓名', '年级', '班级', '学号', '科目名称', '考试名称',
                '班级排名', '年级排名', '成绩', '创建于', '更新于', '状态 . 操作',
            ],
            'uris'           => $this->uris(),
            'exams'          => $examList,
            'classes'        => $classList,
            'subjects'       => $subjectList,
            'importTemplate' => 'files/scores.xlsx',
        ]);
        
    }
}
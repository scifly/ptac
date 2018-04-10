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
//        $exams = Exam::get()->pluck('name', 'id')->toArray();
        $schoolId = $this->schoolId();
        $school = School::whereId($schoolId)->first();
        #获取学校下所有班级 和 考试
        $squadIds = [];
        $examarr = [];
        $examScore = [];
        $squads = $school->classes;
        foreach ($squads as $squad) {
            $squadIds[] = $squad->id;
        }
        #显示的考试
        $examAll = Exam::whereEnabled(1)->get();
        foreach ($examAll as $item) {
            #筛选出属于本校的考试
            if (empty(array_diff(explode(',', $item->class_ids), $squadIds))) {
                $examarr[$item->id] = $item->name;
                if (ScoreTotal::whereExamId($item->id)->first()) {
                    $examScore[$item->id] = $item->name;
                }
            }
        }
        if ($examScore) {
            $ids = Exam::whereId(array_keys($examScore)[0])->first();
            $classes = Squad::whereIn('id', explode(',', $ids['class_ids']))
                ->pluck('name', 'id')
                ->toArray();
            $subjects = Subject::whereIn('id', explode(',', $ids['subject_ids']))
                ->get()
                ->toArray();
        }
        if (empty($examarr)) {
            $examarr[] = '';
        }
        if (empty($classes)) {
            $classes[] = '';
        }
        if (empty($subjects)) {
            $subjects[] = '';
        }
        $view->with([
            'buttons'   => [
                'send'       => [
                    'id'    => 'send',
                    'label' => '成绩发送',
                    'icon'  => 'fa fa-send-o',
                ],
                'import'     => [
                    'id'    => 'import',
                    'label' => '批量导入',
                    'icon'  => 'fa fa-arrow-circle-up',
                ],
                'statistics' => [
                    'id'    => 'statistics',
                    'label' => ' 排名统计',
                    'icon'  => 'fa fa-bar-chart-o',
                ],
            ],
            'titles'    => [
                '#', '姓名', '年级', '班级', '学号', '科目名称', '考试名称',
                '班级排名', '年级排名', '成绩', '创建于', '更新于', '状态 . 操作',
            ],
            'uris'      => $this->uris(),
            'classes'   => $classes,
            'examarr'   => $examarr,
            'examScore' => $examScore,
            'subjects'  => $subjects,
        ]);
        
    }
}
<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Exam;
use App\Models\Grade;
use App\Models\Squad;
use Illuminate\Contracts\View\View;

/**
 * Class ScoreIndexComposer
 * @package App\Http\ViewComposers
 */
class ScoreTotalComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $optionAll = [null => '全部'];
        $htmlClass = $this->singleSelectList(
            array_merge(
                $optionAll,
                Squad::whereIn('id', $this->classIds())->get()->pluck('name', 'id')->toArray()
            ), 'filter_class'
        );
        $htmlGrade = $this->singleSelectList(
            array_merge(
                $optionAll,
                Grade::whereIn('id', $this->gradeIds())->get()->pluck('name', 'id')->toArray()
            ), 'filter_grade'
        );
        $htmlExam = $this->singleSelectList(
            array_merge(
                $optionAll,
                Exam::whereIn('id', $this->examIds())->get()->pluck('name', 'id')->toArray()
            ), 'filter_grade'
        );
        $view->with([
            'titles' => [
                '#', '姓名', '学号',
                ['title' => '年级', 'html' => $htmlGrade],
                ['title' => '班级', 'html' => $htmlClass],
                ['title' => '考试名称', 'html' => $htmlExam],
                '总成绩', '年级排名', '班级排名',
                [
                    'title' => '创建于',
                    'html'  => $this->inputDateTimeRange('创建于'),
                ],
                [
                    'title' => '更新于',
                    'html'  => $this->inputDateTimeRange('更新于'),
                ],
                [
                    'title' => '状态',
                    'html'  => $this->singleSelectList(
                        [null => '全部', 0 => '已禁用', 1 => '已启用'], 'filter_enabled'
                    ),
                ],
            ],
            'filter' => true,
        ]);
        
    }
}
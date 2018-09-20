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
class ScoreTotalIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
        
        $view->with([
            'titles' => [
                '#', '学号', '姓名', '考试名称', '总成绩', '班级排名', '年级排名',
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
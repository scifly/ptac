<?php
namespace App\Http\ViewComposers;

use App\Helpers\ModelTrait;
use App\Models\Grade;
use Illuminate\Contracts\View\View;

/**
 * Class SquadIndexComposer
 * @package App\Http\ViewComposers
 */
class SquadIndexComposer {
    
    use ModelTrait;
    
    /**
     * @param View $view
     */
    public function compose(View $view) {
    
        $view->with([
            'titles' => [
                '#', '名称',
                [
                    'title' => '所属年级',
                    'html' => $this->singleSelectList(
                        [null => '全部'] + Grade::whereIn('id', $this->gradeIds())->pluck('name', 'id')->toArray(),
                        'filter_grade_id'
                    )
                ],
                '班主任',
                [
                    'title' => '创建于',
                    'html'  => $this->inputDateTimeRange('创建于')
                ],
                [
                    'title' => '更新于',
                    'html'  => $this->inputDateTimeRange('更新于')
                ],
                [
                    'title' => '同步状态',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '未同步', 1 => '已同步'], 'filter_subscribed'
                    )
                ],
                [
                    'title' => '状态 . 操作',
                    'html' => $this->singleSelectList(
                        [null => '全部', 0 => '未启用', 1 => '已启用'], 'filter_enabled'
                    )
                ],
            ],
            'filter' => true
        ]);
        
    }
    
}